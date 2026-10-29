<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

/**
 * "Registro" dei campi modificabili dal pannello.
 * Per rendere modificabile un nuovo testo/immagine: aggiungi una riga qui,
 * rilancia `php artisan db:seed --class=PageContentSeeder` e usa la chiave nel frontend.
 *
 * Il seeder NON sovrascrive i valori già modificati dagli admin:
 * aggiorna solo etichetta, tipo e ordine.
 */
class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            // page,  key,           type,    label
            ['news', 'hero_image', 'image', 'Immagine di copertina'],
            ['news', 'title',      'text',  'Titolo'],
            ['news', 'intro',      'html',  'Testo introduttivo'],

            // Testi introduttivi delle sezioni: il primo valore arriva dai
            // dati attuali del sito (data/gio-data.json), poi si cambiano dal pannello
            ['pubblicazioni', 'intro', 'html', 'Testo introduttivo'],
            ['progetti',      'intro', 'html', 'Testo introduttivo'],
            ['divulgazione',  'intro', 'html', 'Testo introduttivo'],

            // Esempi per le altre pagine: aggiungi qui man mano che converti i .vue
            // ['home', 'hero_image', 'image', 'Immagine di copertina'],
            // ['home', 'hero_title', 'text',  'Titolo principale'],
            // ['chi-siamo', 'intro', 'html',  'Testo introduttivo'],
        ];

        // Valori iniziali, usati solo se il campo è ancora vuoto
        $data = json_decode(file_get_contents(__DIR__.'/data/gio-data.json'), true);
        $initial = [
            'pubblicazioni.intro' => $data['pubblicazioni']['intro_text'] ?? null,
            'progetti.intro' => $data['progetti']['intro_text'] ?? null,
            'divulgazione.intro' => $data['divulgazione']['intro_text'] ?? null,
        ];

        foreach ($fields as $sort => [$page, $key, $type, $label]) {
            $content = PageContent::firstOrNew(['page' => $page, 'key' => $key]);
            $content->fill(['type' => $type, 'label' => $label, 'sort' => $sort]);
            if ($content->value === null && isset($initial["{$page}.{$key}"])) {
                $content->value = $initial["{$page}.{$key}"];
            }
            $content->save();
        }
    }
}
