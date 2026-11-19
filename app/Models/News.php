<?php

namespace App\Models;

use App\Support\FileUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'slug', 'title', 'category', 'date', 'image', 'excerpt', 'body',
        'attachments', 'is_published', 'show_on_home', 'home_until',
    ];

    protected $casts = [
        'date' => 'date',
        'attachments' => 'array',
        'is_published' => 'boolean',
        'show_on_home' => 'boolean',
        'home_until' => 'date',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * News da mostrare nel carosello della Home: pubblicate, con «Mostra in
     * Home» acceso e non oltre la data «In Home fino al» (se c'è).
     */
    public function scopeOnHome(Builder $query): Builder
    {
        return $query->published()
            ->where('show_on_home', true)
            ->where(fn (Builder $q) => $q
                ->whereNull('home_until')
                ->orWhereDate('home_until', '>=', today()));
    }

    /** True se in questo momento la news compare in Home */
    public function isOnHome(): bool
    {
        return $this->is_published
            && $this->show_on_home
            && (! $this->home_until || $this->home_until->gte(today()));
    }

    /** Forma usata dal frontend (pages/news, NewsCard, NewsCarousel) */
    public function toApi(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->category,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'image' => FileUrl::to($this->image),
            'date' => $this->date?->format('Y-m-d'),
            'attachments' => collect($this->attachments ?? [])
                ->map(fn (array $a) => [
                    'type' => $a['type'] ?? 'pdf',
                    'label' => $a['label'] ?? '',
                    'url' => FileUrl::to(($a['type'] ?? '') === 'external'
                        ? ($a['url'] ?? null)
                        : ($a['file'] ?? $a['url'] ?? null)),
                ])
                ->filter(fn (array $a) => $a['url'])
                ->values()
                ->all(),
        ];
    }
}
