<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use App\Support\FileUrl;
use App\Support\RichHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Sottosezione di Divulgazione (Corsi, Eventi e convegni…) */
class OutreachSection extends Model
{
    use Sortable;

    protected $fillable = [
        'slug', 'title', 'title_parts', 'aria_label', 'intro_excerpt',
        'intro_text', 'image_path', 'sort',
    ];

    protected $casts = ['title_parts' => 'array'];

    public function items(): HasMany
    {
        return $this->hasMany(OutreachItem::class)->ordered();
    }

    /** Righe del titolo della card: se non indicate, il titolo intero */
    public function titleParts(): array
    {
        $parts = array_values(array_filter($this->title_parts ?? [], fn ($p) => trim((string) $p) !== ''));

        return $parts ?: [$this->title];
    }

    public function toCard(): array
    {
        return array_filter([
            'slug' => $this->slug,
            'title' => $this->title,
            'title_parts' => $this->titleParts(),
            'aria_label' => $this->aria_label ?: "Vai alla pagina {$this->title}",
            'intro_excerpt' => $this->intro_excerpt,
            'image_path' => FileUrl::to($this->image_path),
        ], fn ($v) => $v !== null);
    }

    public function toApi(): array
    {
        return $this->toCard() + [
            'intro_text' => RichHtml::render($this->intro_text) ?? '',
            'items' => $this->items->map->toApi()->all(),
        ];
    }
}
