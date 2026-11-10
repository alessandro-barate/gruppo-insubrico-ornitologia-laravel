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

    /** Campi "html" in cui si possono inserire immagini nel testo (pagina.chiave). */
    public const HTML_WITH_IMAGES = ['chi-siamo.board'];

    /** Questo campo accetta immagini nel testo? */
    public function allowsImages(): bool
    {
        return $this->type === 'html'
            && in_array($this->page.'.'.$this->key, self::HTML_WITH_IMAGES, true);
    }

    /** Tag ammessi per questo campo */
    public function allowedTags(): string
    {
        return self::ALLOWED_TAGS.($this->allowsImages() ? '<img>' : '');
    }

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
