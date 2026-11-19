<?php

namespace App\Models;

use App\Support\FileUrl;
use Illuminate\Database\Eloquent\Model;

/**
 * Un anno della Trasparenza sui contributi pubblici: anno, PDF con
 * l'elenco dei contributi ricevuti e una nota facoltativa.
 */
class PublicContribution extends Model
{
    protected $fillable = ['year', 'file_path', 'note'];

    protected $casts = ['year' => 'integer'];

    public function toApi(): array
    {
        return [
            'year' => $this->year,
            'note' => $this->note,
            'file_url' => FileUrl::to($this->file_path),
        ];
    }
}
