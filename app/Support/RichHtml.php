<?php

namespace App\Support;

/**
 * Prepara per il sito l'HTML scritto nell'editor del pannello.
 *
 * Il frontend dà lo stile ai testi tramite alcune classi (specific-list,
 * specific-link, subtitle, space-line). L'editor del pannello non le
 * conserva su elenchi, titoli e linee: qui le rimettiamo, così un testo
 * modificato dal pannello ha lo stesso aspetto di prima.
 */
class RichHtml
{
    private const DEFAULT_CLASSES = [
        'ul' => 'specific-list',
        'a' => 'specific-link',
        'h2' => 'subtitle',
        'hr' => 'space-line',
    ];

    public static function render(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $html = self::images($html);

        foreach (self::DEFAULT_CLASSES as $tag => $class) {
            // <ul> o <ul ...> senza un attributo class → aggiunge la classe
            $html = preg_replace_callback(
                '/<'.$tag.'(\s[^>]*)?(\/?)>/i',
                function (array $m) use ($class) {
                    $attrs = $m[1] ?? '';
                    if (preg_match('/\sclass\s*=/i', $attrs)) {
                        return $m[0];
                    }

                    return '<'.strtolower(substr($m[0], 1, strcspn($m[0], " \t\n/>", 1))).' class="'.$class.'"'.$attrs.($m[2] ?? '').'>';
                },
                $html,
            );
        }

        return $html;
    }

    /**
     * Immagini inserite con l'editor del pannello: l'editor salva il percorso
     * del file in data-id. Qui ogni <img> viene riscritto con il solo
     * indirizzo pubblico del file e la descrizione (alt); ogni altro
     * attributo si toglie. Un'immagine senza file valido viene tolta.
     */
    private static function images(string $html): string
    {
        return preg_replace_callback('/<img\b[^>]*>/i', function (array $m) {
            $attr = fn (string $name) => preg_match('/\s'.$name.'\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $m[0], $a)
                ? html_entity_decode($a[2] !== '' ? $a[2] : ($a[3] ?? ''), ENT_QUOTES)
                : '';

            $path = $attr('data-id');
            $src = $path !== '' ? FileUrl::to($path) : $attr('src');

            if (! is_string($src) || ! preg_match('#^(https?://|/)#i', $src)) {
                return '';
            }

            return '<img src="'.e($src).'" alt="'.e($attr('alt')).'" loading="lazy">';
        }, $html);
    }
}
