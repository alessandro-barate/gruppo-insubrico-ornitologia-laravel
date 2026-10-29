<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use App\Support\FileUrl;
use App\Support\RichHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectSection extends Model
{
    use Sortable;

    /**
     * In queste sezioni la pagina Progetti mostra solo i testi: i PDF delle
     * ricerche si consultano nella bibliografia di Altre Pubblicazioni.
     * Stessa regola di DESCRIPTION_ONLY_SECTIONS in useProgetti.ts.
     */
    public const DESCRIPTION_ONLY = ['ricerca-in-corso', 'ricerca-terminati'];

    protected $fillable = [
        'slug', 'title', 'intro_excerpt', 'intro_text', 'image_path',
        'has_projects', 'projects_are_navigable', 'sort',
    ];

    protected $casts = [
        'has_projects' => 'boolean',
        'projects_are_navigable' => 'boolean',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->ordered();
    }

    public function toApi(): array
    {
        return array_filter([
            'slug' => $this->slug,
            'title' => $this->title,
            'intro_text' => RichHtml::render($this->intro_text),
            'intro_excerpt' => $this->intro_excerpt,
            'image_path' => FileUrl::to($this->image_path),
            'has_projects' => $this->has_projects,
            'projects_are_navigable' => $this->projects_are_navigable,
        ], fn ($v) => $v !== null);
    }

    public function toCard(): array
    {
        return array_filter([
            'slug' => $this->slug,
            'title' => $this->title,
            'intro_excerpt' => $this->intro_excerpt,
            'image_path' => FileUrl::to($this->image_path),
        ], fn ($v) => $v !== null);
    }
}
