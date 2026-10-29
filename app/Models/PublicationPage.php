<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicationPage extends Model
{
    use Sortable;

    protected $fillable = ['publication_section_id', 'image_path', 'alt', 'sort'];

    public function sortScope(Builder $query): Builder
    {
        return $query->where('publication_section_id', $this->publication_section_id);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PublicationSection::class, 'publication_section_id');
    }
}
