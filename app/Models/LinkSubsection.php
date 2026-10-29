<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Colonna di un pannello della pagina Social e link utili */
class LinkSubsection extends Model
{
    use Sortable;

    // Chiavi usate da pages/links.vue
    public const CATEGORIES = [
        'portali' => 'Portali di osservazioni e citizen science',
        'organizzazioni' => 'Organizzazioni internazionali e scientifiche',
        'reti' => 'Italia - reti, associazioni e gruppi regionali',
        'turismo' => 'Turismo naturalistico',
    ];

    protected $fillable = ['category', 'title', 'groups', 'sort'];

    protected $casts = ['groups' => 'array'];

    public function sortScope(Builder $query): Builder
    {
        return $query->where('category', $this->category);
    }

    public function toApi(): array
    {
        return [
            'title' => $this->title,
            'groups' => collect($this->groups ?? [])
                ->map(fn (array $g) => array_filter([
                    'title' => ($g['title'] ?? null) ?: null,
                    'links' => collect($g['links'] ?? [])
                        ->filter(fn ($l) => trim((string) ($l['href'] ?? '')) !== '')
                        ->map(fn ($l) => array_filter([
                            'title' => $l['title'] ?? '',
                            // il frontend mostra il sottotitolo subito dopo il titolo
                            'subtitle' => ($l['subtitle'] ?? null) ?: null,
                            'href' => $l['href'],
                        ], fn ($v) => $v !== null))
                        ->values()->all(),
                ], fn ($v) => $v !== null))
                ->values()->all(),
        ];
    }
}
