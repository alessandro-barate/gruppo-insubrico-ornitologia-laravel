<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Colonna "sort" per l'ordinamento a trascinamento nel pannello.
 * Un nuovo record va in fondo alla lista.
 */
trait Sortable
{
    public static function bootSortable(): void
    {
        static::creating(function ($model) {
            if (! $model->sort) {
                $model->sort = (int) static::query()
                    ->when(method_exists($model, 'sortScope'), fn (Builder $q) => $model->sortScope($q))
                    ->max('sort') + 1;
            }
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
