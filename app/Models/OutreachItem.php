<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use App\Support\FileUrl;
use App\Support\RichHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutreachItem extends Model
{
    use Sortable;

    protected $fillable = [
        'outreach_section_id', 'title', 'year', 'body', 'image_path', 'links', 'sort',
    ];

    protected $casts = ['links' => 'array'];

    public function sortScope(Builder $query): Builder
    {
        return $query->where('outreach_section_id', $this->outreach_section_id);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(OutreachSection::class, 'outreach_section_id');
    }

    public function toApi(): array
    {
        $links = collect($this->links ?? [])
            ->map(fn (array $l) => [
                'label' => $l['label'] ?? '',
                'url' => FileUrl::to($l['file'] ?? $l['url'] ?? null),
            ])
            ->filter(fn (array $l) => $l['url'])
            ->values()->all();

        return array_filter([
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'body' => RichHtml::render($this->body),
            'image_path' => FileUrl::to($this->image_path),
            'links' => $links ?: null,
        ], fn ($v) => $v !== null);
    }
}
