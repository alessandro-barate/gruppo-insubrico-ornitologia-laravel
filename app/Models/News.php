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
        'attachments', 'is_published',
    ];

    protected $casts = [
        'date' => 'date',
        'attachments' => 'array',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
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
