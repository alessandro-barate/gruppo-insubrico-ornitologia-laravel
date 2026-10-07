<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PageContent extends Model
{
    protected $fillable = ['page', 'key', 'type', 'label', 'value', 'sort'];

    protected $appends = ['public_value'];

    /** Tag HTML ammessi nei campi di tipo "html". */
    public const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3>';

    /**
     * Valore pronto per il frontend:
     * - immagini → URL completo (http://.../storage/page-contents/xxx.webp)
     * - testi    → così come sono
     * - null     → il frontend userà il proprio default
     */
    public function getPublicValueAttribute(): ?string
    {
        if ($this->value === null || $this->value === '') {
            return null;
        }

        return $this->type === 'image'
            ? Storage::disk('public')->url($this->value)
            : $this->value;
    }
}
