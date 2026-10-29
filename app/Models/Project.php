<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use App\Support\FileUrl;
use App\Support\RichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    use Sortable;

    protected $fillable = [
        'project_section_id', 'slug', 'title', 'image_path', 'intro', 'blocks', 'sort',
    ];

    protected $casts = ['blocks' => 'array'];

    public function sortScope(Builder $query): Builder
    {
        return $query->where('project_section_id', $this->project_section_id);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ProjectSection::class, 'project_section_id');
    }

    /** Blocchi con gli indirizzi dei link già risolti; link vuoti scartati */
    public function apiBlocks(): array
    {
        return collect($this->blocks ?? [])
            ->map(function (array $block) {
                $links = collect($block['links'] ?? [])
                    ->map(fn (array $l) => [
                        'kind' => $l['kind'] ?? 'pdf',
                        'label' => $l['label'] ?? '',
                        'url' => FileUrl::to(($l['kind'] ?? '') === 'external'
                            ? ($l['url'] ?? null)
                            : ($l['file'] ?? $l['url'] ?? null)),
                    ])
                    ->filter(fn (array $l) => $l['url'])
                    ->values()->all();

                // "<p></p>" lasciato dall'editor = nessun testo
                $text = $block['text'] ?? null;
                if ($text !== null && trim(strip_tags($text)) === '') {
                    $text = null;
                }

                return array_filter([
                    'text' => RichHtml::render($text),
                    'links' => $links,
                ], fn ($v) => $v !== null && $v !== '');
            })
            ->filter(fn (array $b) => ! empty($b['text']) || ! empty($b['links']))
            ->values()->all();
    }

    /** Come Project in useProgetti.ts */
    public function toApi(): array
    {
        return array_filter([
            'id' => $this->id,
            'section_slug' => $this->section?->slug,
            'slug' => $this->slug,
            'title' => $this->title,
            'image_path' => FileUrl::to($this->image_path),
            'intro' => RichHtml::render($this->intro),
            'blocks' => $this->apiBlocks(),
            'order' => $this->sort,
        ], fn ($v) => $v !== null);
    }

    public function toCard(): array
    {
        return array_filter([
            'slug' => $this->slug,
            'title' => $this->title,
            'intro_excerpt' => $this->intro
                ? Str::substr(trim(strip_tags($this->intro)), 0, 120)
                : null,
            'image_path' => FileUrl::to($this->image_path),
        ], fn ($v) => $v !== null);
    }
}
