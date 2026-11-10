<?php

namespace App\Support;

use App\Models\GalleryImage;
use App\Models\News;
use App\Models\OutreachItem;
use App\Models\OutreachSection;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\ProjectSection;
use App\Models\PublicationItem;
use App\Models\PublicationPage;
use App\Models\PublicationSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pulizia dei file caricati dal pannello.
 *
 * Quando un'immagine o un documento viene sostituito, tolto da un campo
 * o il record viene eliminato, il file vecchio si cancella dal disco
 * "public", ma solo se:
 *  - è un file caricato in Laravel (non "/documents/…" del frontend né
 *    un link esterno);
 *  - nessun altro contenuto lo usa ancora (lo stesso file può essere
 *    condiviso, es. dopo gio:import-files).
 *
 * Le sezioni con figli (pubblicazioni, progetti, divulgazione) vengono
 * eliminate un record alla volta, così anche i file dei figli si puliscono:
 * la cancellazione "a cascata" del database non avviserebbe Laravel.
 */
class StoredFiles
{
    /** Colonne che contengono direttamente il percorso di un file */
    public const COLUMNS = [
        News::class => ['image'],
        PublicationSection::class => ['image_path', 'header_image', 'pdf_path'],
        PublicationItem::class => ['image_path', 'file_path'],
        PublicationPage::class => ['image_path'],
        ProjectSection::class => ['image_path'],
        Project::class => ['image_path'],
        GalleryImage::class => ['image_path'],
        OutreachSection::class => ['image_path'],
        OutreachItem::class => ['image_path'],
    ];

    /** Colonne JSON con elenchi di file (chiave "file" di ogni voce) */
    public const JSON_COLUMNS = [
        News::class => ['attachments'],
        Project::class => ['blocks'],
        OutreachItem::class => ['links'],
    ];

    public static function register(): void
    {
        $models = array_unique(array_merge(
            array_keys(self::COLUMNS),
            array_keys(self::JSON_COLUMNS),
            [PageContent::class],
        ));

        foreach ($models as $model) {
            // In "updated" getOriginal() ha ancora i valori di prima
            $model::updated(function (Model $record) {
                $before = self::pathsOf($record, fn (string $key) => $record->getOriginal($key));
                $after = self::pathsOf($record, fn (string $key) => $record->getAttribute($key));
                self::deleteUnused(array_diff($before, $after));
            });

            $model::deleted(function (Model $record) {
                self::deleteUnused(self::pathsOf($record, fn (string $key) => $record->getAttribute($key)));
            });
        }

        // Figli eliminati uno per uno prima del genitore (vedi sopra)
        PublicationSection::deleting(function (PublicationSection $section) {
            $section->children()->get()->each->delete();
            $section->pages()->get()->each->delete();
            $section->items()->get()->each->delete();
        });
        ProjectSection::deleting(fn (ProjectSection $s) => $s->projects()->get()->each->delete());
        OutreachSection::deleting(fn (OutreachSection $s) => $s->items()->get()->each->delete());
    }

    /**
     * Percorsi dei file di un record, letti con $value (valori attuali o
     * originali).
     *
     * @return string[]
     */
    private static function pathsOf(Model $record, callable $value): array
    {
        $class = $record::class;
        $paths = [];

        foreach (self::COLUMNS[$class] ?? [] as $column) {
            $paths[] = $value($column);
        }

        foreach (self::JSON_COLUMNS[$class] ?? [] as $column) {
            $paths = array_merge($paths, self::filesInJson($value($column)));
        }

        // Contenuti modificabili delle pagine: le immagini e le foto
        // inserite nei testi formattati (es. il direttivo in Chi siamo)
        if ($record instanceof PageContent && $value('type') === 'image') {
            $paths[] = $value('value');
        }
        if ($record instanceof PageContent && $value('type') === 'html') {
            $paths = array_merge($paths, self::imagesInHtml($value('value')));
        }

        return array_values(array_unique(array_filter($paths, fn ($p) => self::isStored($p))));
    }

    /** Percorsi delle immagini inserite con l'editor (attributo data-id) */
    private static function imagesInHtml(mixed $html): array
    {
        if (! is_string($html) || ! preg_match_all('/<img\b[^>]*\sdata-id\s*=\s*"([^"]+)"/i', $html, $m)) {
            return [];
        }

        return array_map(fn ($p) => html_entity_decode($p, ENT_QUOTES), $m[1]);
    }

    /** Valori "file" a qualsiasi livello di un array (o JSON) */
    private static function filesInJson(mixed $data): array
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (! is_array($data)) {
            return [];
        }

        $files = [];
        array_walk_recursive($data, function ($v, $k) use (&$files) {
            if ($k === 'file' && is_string($v)) {
                $files[] = $v;
            }
        });

        return $files;
    }

    /** File caricato in Laravel (non del frontend, non un link) */
    private static function isStored(mixed $path): bool
    {
        return is_string($path)
            && trim($path) !== ''
            && ! Str::startsWith($path, ['/', 'http://', 'https://']);
    }

    /** @param string[] $paths */
    private static function deleteUnused(array $paths): void
    {
        $disk = Storage::disk('public');

        foreach ($paths as $path) {
            if (! self::isUsed($path) && $disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }

    /** Il file è ancora usato da qualche contenuto? (nel dubbio sì) */
    private static function isUsed(string $path): bool
    {
        foreach (self::COLUMNS as $model => $columns) {
            foreach ($columns as $column) {
                if ($model::where($column, $path)->exists()) {
                    return true;
                }
            }
        }

        // Nel JSON salvato le "/" possono essere scritte come "\/": cerco solo
        // il nome del file. Al massimo trova un omonimo e il file resta.
        $name = basename($path);

        foreach (self::JSON_COLUMNS as $model => $columns) {
            foreach ($columns as $column) {
                if ($model::where($column, 'like', '%'.$name.'%')->exists()) {
                    return true;
                }
            }
        }

        return PageContent::where('type', 'image')->where('value', $path)->exists()
            || PageContent::where('type', 'html')->where('value', 'like', '%'.$name.'%')->exists();
    }
}
