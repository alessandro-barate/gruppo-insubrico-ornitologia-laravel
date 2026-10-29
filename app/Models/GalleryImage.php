<?php

namespace App\Models;

use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use Sortable;

    protected $fillable = ['gallery', 'image_path', 'alt', 'caption', 'sort'];

    public function sortScope(Builder $query): Builder
    {
        return $query->where('gallery', $this->gallery);
    }
}
