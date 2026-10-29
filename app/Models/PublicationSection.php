<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use App\Support\FileUrl;
use App\Support\RichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Sezione di Pubblicazioni. È un albero: le sezioni "group" contengono
 * altre sezioni; le altre contengono voci (items) o, per "detail",
 * un contenuto singolo con le sue pagine interne.
 */
class PublicationSection extends Model
{
    use Sortable;

    public const TYPES = [
        'group' => 'Contenitore di altre sezioni',
        'detail' => 'Pagina singola (es. un Quaderno)',
        'pdf-list' => 'Lista di PDF (es. BOL, Resoconti)',
        'bibliography' => 'Bibliografia',
        'cards' => 'Schede con titolo, immagine e testo',
    ];

    protected $fillable = [
        'parent_id', 'slug', 'title', 'type', 'intro_excerpt', 'intro_text',
        'second_intro_text', 'image_path', 'header_image', 'items_per_page',
        'group_by_year', 'body', 'pdf_path', 'price', 'sort',
    ];

    protected $casts = [
        'group_by_year' => 'boolean',
        'items_per_page' => 'integer',
    ];

    public function sortScope(Builder $query): Builder
    {
        return $query->where('parent_id', $this->parent_id);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    public function items(): HasMany
    {
        return $this->hasMany(PublicationItem::class)->ordered();
    }

    public function pages(): HasMany
    {
        return $this->hasMany(PublicationPage::class)->ordered();
    }

    /** "/pubblicazioni/uccelli-varese/resoconti-ornitologici" */
    public function path(): string
    {
        $slugs = [];
        $node = $this;
        while ($node) {
            array_unshift($slugs, $node->slug);
            $node = $node->parent;
        }

        return '/pubblicazioni/'.implode('/', $slugs);
    }

    /** Per i menu a tendina del pannello: "Uccelli della Provincia di Varese › Resoconti" */
    public function fullTitle(): string
    {
        return $this->parent ? $this->parent->fullTitle().' › '.$this->title : $this->title;
    }

    /** Card di navigazione (home Pubblicazioni e sezioni "group") */
    public function toCard(): array
    {
        return array_filter([
            'slug' => $this->slug,
            'title' => $this->title,
            'intro_excerpt' => $this->intro_excerpt,
            'image_path' => FileUrl::to($this->image_path),
            'type' => $this->type,
        ], fn ($v) => $v !== null);
    }

    /** Nodo completo, come PubNode in usePubblicazioni.ts */
    public function toNode(): array
    {
        $node = [
            'slug' => $this->slug,
            'title' => $this->title,
            'type' => $this->type,
            'intro_text' => RichHtml::render($this->intro_text),
            'second_intro_text' => $this->second_intro_text,
            'intro_excerpt' => $this->intro_excerpt,
            'header_image' => FileUrl::to($this->header_image),
            'image_path' => FileUrl::to($this->image_path),
            'items_per_page' => $this->items_per_page,
        ];

        switch ($this->type) {
            case 'group':
                $node['children'] = $this->children->map->toCard()->all();
                break;

            case 'detail':
                $node['body'] = RichHtml::render($this->body);
                $node['pdf_url'] = FileUrl::to($this->pdf_path);
                $node['price'] = $this->price;
                $node['inner_pages'] = $this->pages->map(fn (PublicationPage $p) => array_filter([
                    'id' => $p->id,
                    'image_path' => FileUrl::to($p->image_path),
                    'alt' => $p->alt,
                ], fn ($v) => $v !== null))->all();
                break;

            case 'pdf-list':
                $node['group_by_year'] = $this->group_by_year;
                $node['items'] = $this->items
                    ->filter(fn (PublicationItem $i) => $i->file_path)
                    ->map(fn (PublicationItem $i) => array_filter([
                        'id' => $i->id,
                        'year' => $i->year,
                        'title' => $i->title,
                        'pdf_url' => FileUrl::to($i->file_path),
                    ], fn ($v) => $v !== null))
                    ->values()->all();
                break;

            case 'bibliography':
                $node['items'] = $this->items->map(fn (PublicationItem $i) => array_filter([
                    'id' => $i->id,
                    'text' => $i->text,
                    'pdf_url' => FileUrl::to($i->file_path),
                ], fn ($v) => $v !== null))->all();
                break;

            case 'cards':
                $node['items'] = $this->items->map(fn (PublicationItem $i) => array_filter([
                    'id' => $i->id,
                    'title' => $i->title,
                    'body' => RichHtml::render($i->text) ?? '',
                    'image_path' => FileUrl::to($i->image_path),
                ], fn ($v) => $v !== null))->all();
                break;
        }

        return array_filter($node, fn ($v) => $v !== null);
    }
}
