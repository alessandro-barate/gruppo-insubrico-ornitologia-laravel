<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use App\Models\LinkSubsection;
use App\Models\News;
use App\Models\OutreachSection;
use App\Models\ProjectSection;
use App\Models\PublicationSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Carica nel database i contenuti attuali del sito, esportati dai dati
 * finti dei composable Nuxt in database/seeders/data/gio-data.json.
 *
 *   php artisan db:seed --class=ContentSeeder
 *
 * Sicuro da rilanciare: una sezione che ha già dei dati (es. le news)
 * viene saltata, così non si cancella nulla di ciò che gli admin hanno
 * inserito dal pannello.
 *
 * I percorsi dei file restano quelli del frontend ("/documents/…"):
 * per spostare i file in Laravel usa `php artisan gio:import-files`.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(__DIR__.'/data/gio-data.json'), true);

        $this->once('News', News::class, fn () => $this->news($data['news']));
        $this->once('Pubblicazioni', PublicationSection::class,
            fn () => $this->publications($data['pubblicazioni']['tree'], null));
        $this->once('Progetti', ProjectSection::class, fn () => $this->projects($data['progetti']));
        $this->once('Galleria Progetti', GalleryImage::class, fn () => $this->gallery($data['progetti']['gallery']));
        $this->once('Divulgazione', OutreachSection::class, fn () => $this->outreach($data['divulgazione']['subsections']));
        $this->once('Link utili', LinkSubsection::class, fn () => $this->links($data['links']));
    }

    private function once(string $label, string $model, callable $seed): void
    {
        if ($model::query()->exists()) {
            $this->command?->warn("{$label}: già presenti, saltati");

            return;
        }

        DB::transaction($seed);
        $this->command?->info("{$label}: caricati");
    }

    private function news(array $items): void
    {
        foreach ($items as $n) {
            News::create([
                'slug' => $n['slug'],
                'title' => $n['title'],
                'category' => $n['category'] ?? null,
                'date' => $n['date'],
                'image' => $n['image'] ?? null,
                'excerpt' => $n['excerpt'] ?? null,
                'body' => $n['body'] ?? null,
                'attachments' => array_map(fn ($a) => ($a['type'] ?? '') === 'external'
                    ? ['type' => 'external', 'label' => $a['label'] ?? '', 'url' => $a['url'] ?? null]
                    : ['type' => $a['type'] ?? 'pdf', 'label' => $a['label'] ?? '', 'file' => $a['url'] ?? null],
                    $n['attachments'] ?? []),
            ]);
        }
    }

    private function publications(array $nodes, ?PublicationSection $parent): void
    {
        foreach ($nodes as $sort => $n) {
            $section = PublicationSection::create([
                'parent_id' => $parent?->id,
                'slug' => $n['slug'],
                'title' => $n['title'],
                'type' => $n['type'],
                'intro_excerpt' => $n['intro_excerpt'] ?? null,
                'intro_text' => $n['intro_text'] ?? null,
                'second_intro_text' => $n['second_intro_text'] ?? null,
                'image_path' => $n['image_path'] ?? null,
                'header_image' => $n['header_image'] ?? null,
                'items_per_page' => $n['items_per_page'] ?? null,
                'group_by_year' => $n['group_by_year'] ?? false,
                'body' => $n['body'] ?? null,
                'pdf_path' => ($n['pdf_url'] ?? '') ?: null,
                'price' => ($n['price'] ?? '') ?: null,
                'sort' => $sort + 1,
            ]);

            if ($n['type'] === 'group') {
                $this->publications($n['children'] ?? [], $section);

                continue;
            }

            foreach ($n['inner_pages'] ?? [] as $i => $p) {
                $section->pages()->create([
                    'image_path' => $p['image_path'],
                    'alt' => $p['alt'] ?? null,
                    'sort' => $i + 1,
                ]);
            }

            foreach ($n['items'] ?? [] as $i => $item) {
                $section->items()->create(match ($n['type']) {
                    'pdf-list' => [
                        'title' => $item['title'],
                        'year' => $item['year'] ?? null,
                        'file_path' => ($item['pdf_url'] ?? '') ?: null,
                    ],
                    'bibliography' => [
                        'text' => $item['text'],
                        'file_path' => ($item['pdf_url'] ?? '') ?: null,
                    ],
                    default => [ // cards
                        'title' => $item['title'] ?? null,
                        'text' => $item['body'] ?? null,
                        'image_path' => $item['image_path'] ?? null,
                    ],
                } + ['sort' => $i + 1]);
            }
        }
    }

    private function projects(array $data): void
    {
        $sections = [];
        foreach ($data['sections'] as $sort => $s) {
            $sections[$s['slug']] = ProjectSection::create([
                'slug' => $s['slug'],
                'title' => $s['title'],
                'intro_excerpt' => $s['intro_excerpt'] ?? null,
                'intro_text' => $s['intro_text'] ?? null,
                'image_path' => $s['image_path'] ?? null,
                'has_projects' => $s['has_projects'] ?? true,
                'projects_are_navigable' => $s['projects_are_navigable'] ?? false,
                'sort' => $sort + 1,
            ]);
        }

        $sortBySection = [];
        foreach ($data['projects'] as $p) {
            // Progetti di sezioni non attive sul sito (es. "altri-lavori"): saltati
            $section = $sections[$p['section_slug']] ?? null;
            if (! $section) {
                continue;
            }

            $section->projects()->create([
                'slug' => $p['slug'],
                'title' => $p['title'],
                'image_path' => $p['image_path'] ?? null,
                'intro' => $p['intro'] ?? null,
                'blocks' => array_map(fn ($b) => [
                    'text' => $b['text'] ?? null,
                    'links' => array_values(array_filter(array_map(fn ($l) => ($l['kind'] ?? '') === 'external'
                        ? ['kind' => 'external', 'label' => $l['label'] ?? '', 'url' => $l['url'] ?? null]
                        : ['kind' => $l['kind'] ?? 'pdf', 'label' => $l['label'] ?? '', 'file' => $l['url'] ?? null],
                        $b['links'] ?? []), fn ($l) => ! empty($l['url'] ?? $l['file'] ?? null))),
                ], $p['blocks'] ?? []),
                'sort' => $sortBySection[$section->id] = ($sortBySection[$section->id] ?? 0) + 1,
            ]);
        }
    }

    private function gallery(array $images): void
    {
        foreach ($images as $sort => $g) {
            GalleryImage::create([
                'gallery' => 'progetti',
                'image_path' => $g['src'],
                'alt' => $g['alt'] ?? null,
                'caption' => $g['caption'] ?? null,
                'sort' => $sort + 1,
            ]);
        }
    }

    private function outreach(array $subsections): void
    {
        foreach ($subsections as $sort => $s) {
            $section = OutreachSection::create([
                'slug' => $s['slug'],
                'title' => $s['title'],
                'title_parts' => $s['title_parts'] ?? null,
                'aria_label' => $s['aria_label'] ?? null,
                'intro_excerpt' => $s['intro_excerpt'] ?? null,
                'intro_text' => $s['intro_text'] ?? null,
                'image_path' => $s['image_path'] ?? null,
                'sort' => $sort + 1,
            ]);

            foreach ($s['items'] ?? [] as $i => $item) {
                $section->items()->create([
                    'title' => $item['title'],
                    'year' => $item['year'] ?? null,
                    // "<p></p>" = nessun testo
                    'body' => trim(strip_tags($item['body'] ?? '')) === '' ? null : $item['body'],
                    'image_path' => $item['image_path'] ?? null,
                    'links' => array_map(fn ($l) => ['label' => $l['label'] ?? '', 'file' => $l['url'] ?? null],
                        $item['links'] ?? []),
                    'sort' => $i + 1,
                ]);
            }
        }
    }

    private function links(array $byCategory): void
    {
        foreach ($byCategory as $category => $subsections) {
            foreach ($subsections as $sort => $sub) {
                LinkSubsection::create([
                    'category' => $category,
                    'title' => $sub['title'],
                    'groups' => $sub['groups'] ?? [],
                    'sort' => $sort + 1,
                ]);
            }
        }
    }
}
