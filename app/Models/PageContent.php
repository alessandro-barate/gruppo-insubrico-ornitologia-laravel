<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\FileUrl;
use App\Support\RichHtml;

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
    /**
     * Valore di un campo, con un default se vuoto o non registrato.
     * Es. PageContent::valueFor('pubblicazioni', 'intro', '').
     */
    public static function valueFor(string $page, string $key, ?string $default = null): ?string
    {
        $content = static::where('page', $page)->where('key', $key)->first();

        return $content?->public_value ?? $default;
    }

    public function getPublicValueAttribute(): ?string
    {
        if ($this->value === null || $this->value === '') {
            return null;
        }

        return match ($this->type) {
            'image' => FileUrl::to($this->value),
            'html' => RichHtml::render($this->value),
            default => $this->value,
        };
    }
}
