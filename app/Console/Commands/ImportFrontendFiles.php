<?php

namespace App\Console\Commands;

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
use App\Services\SiteSearch;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Copia in Laravel i PDF e le immagini che oggi stanno in public/ del
 * frontend Nuxt, e aggiorna i percorsi nel database.
 *
 *   php artisan gio:import-files /percorso/del/frontend/public
 *   php artisan gio:import-files /percorso/del/frontend/public --dry-run
 *
 * "/documents/progetti/x.pdf" → storage/app/public/documents/progetti/x.pdf
 * e nel database diventa "documents/progetti/x.pdf" (URL: /storage/…).
 * I file che non trova restano come sono e vengono elencati.
 * Si può rilanciare: i percorsi già importati vengono ignorati.
 */
class ImportFrontendFiles extends Command
{
    protected $signature = 'gio:import-files {source : Cartella public/ del frontend Nuxt} {--dry-run : Mostra cosa farebbe senza copiare nulla}';

    protected $description = 'Copia i file del frontend nello storage di Laravel e aggiorna i percorsi';

    private string $source;

    private int $copied = 0;

    private array $missing = [];

    public function handle(): int
    {
        $this->source = rtrim($this->argument('source'), '/');

        if (! is_dir($this->source)) {
            $this->error("Cartella non trovata: {$this->source}");

            return self::FAILURE;
        }

        // Colonne con un percorso semplice
        $columns = [
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

        foreach ($columns as $model => $fields) {
            $model::query()->each(function (Model $record) use ($fields) {
                foreach ($fields as $field) {
                    $record->{$field} = $this->import($record->{$field});
                }
                $this->save($record);
            });
        }

        // Percorsi dentro le colonne JSON
        News::query()->each(function (News $n) {
            $n->attachments = $this->importList($n->attachments);
            $this->save($n);
        });

        Project::query()->each(function (Project $p) {
            $p->blocks = collect($p->blocks ?? [])->map(function (array $b) {
                $b['links'] = $this->importList($b['links'] ?? []);

                return $b;
            })->all();
            $this->save($p);
        });

        OutreachItem::query()->each(function (OutreachItem $i) {
            $i->links = $this->importList($i->links);
            $this->save($i);
        });

        PageContent::where('type', 'image')->each(function (PageContent $c) {
            $c->value = $this->import($c->value);
            $this->save($c);
        });

        SiteSearch::flush();

        $this->info(($this->option('dry-run') ? 'Da copiare: ' : 'Copiati: ').$this->copied.' file');

        if ($this->missing) {
            $this->warn('Non trovati nel frontend (percorso lasciato com\'era):');
            foreach (array_unique($this->missing) as $path) {
                $this->line("  {$path}");
            }
        }

        return self::SUCCESS;
    }

    private function importList(?array $items): array
    {
        return collect($items ?? [])->map(function (array $item) {
            if (! empty($item['file'])) {
                $item['file'] = $this->import($item['file']);
            }

            return $item;
        })->all();
    }

    /** Copia un file e restituisce il nuovo percorso (o quello vecchio) */
    private function import(?string $path): ?string
    {
        if (! $path || ! Str::startsWith($path, '/') || Str::startsWith($path, '//')) {
            return $path; // vuoto, già importato o link esterno
        }

        $relative = ltrim(rawurldecode($path), '/');
        $from = $this->source.'/'.$relative;

        if (! is_file($from)) {
            $this->missing[] = $path;

            return $path;
        }

        $this->copied++;

        if (! $this->option('dry-run') && ! Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->put($relative, fopen($from, 'r'));
        }

        return $relative;
    }

    private function save(Model $record): void
    {
        if (! $this->option('dry-run') && $record->isDirty()) {
            $record->saveQuietly();
        }
    }
}
