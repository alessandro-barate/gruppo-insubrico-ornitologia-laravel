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

            // Esempi per le altre pagine: aggiungi qui man mano che converti i .vue
            // ['home', 'hero_image', 'image', 'Immagine di copertina'],
            // ['home', 'hero_title', 'text',  'Titolo principale'],
            // ['chi-siamo', 'intro', 'html',  'Testo introduttivo'],
        ];

        foreach ($fields as $sort => [$page, $key, $type, $label]) {
            $content = PageContent::firstOrNew(['page' => $page, 'key' => $key]);
            $content->fill(['type' => $type, 'label' => $label, 'sort' => $sort]);
            $content->save();
        }
    }
}
