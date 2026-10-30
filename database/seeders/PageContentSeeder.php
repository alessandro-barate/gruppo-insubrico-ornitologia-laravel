<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

/**
 * "Registro" dei campi modificabili dal pannello (Pagine → Testi delle pagine).
 * Per rendere modificabile un nuovo testo/immagine: aggiungi una riga qui,
 * rilancia `php artisan db:seed --class=PageContentSeeder` e usa la chiave nel
 * frontend con usePageContent('pagina') → c('chiave', default).
 *
 * Il seeder NON sovrascrive i valori già modificati dagli admin:
 * aggiorna solo etichetta, tipo e ordine. L'ordine delle righe qui è
 * l'ordine in cui i campi compaiono nel pannello.
 *
 * Tipi: text = riga di testo, html = testo formattato, image = immagine.
 */
class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            // ─── Tutto il sito (menu e footer) ──────────────────────────
            ['sito', 'menu_image',   'image', 'Menu a tutto schermo (telefono e tablet) · immagine di sfondo'],
            ['sito', 'footer_image', 'image', 'Footer · immagine di sfondo'],

            // ─── Home ───────────────────────────────────────────────────
            ['home', 'hero_1', 'image', 'Copertina · immagine 1 di 4 (si alternano ogni 4 secondi)'],
            ['home', 'hero_2', 'image', 'Copertina · immagine 2 di 4'],
            ['home', 'hero_3', 'image', 'Copertina · immagine 3 di 4'],
            ['home', 'hero_4', 'image', 'Copertina · immagine 4 di 4'],
            ...self::homeCards(),
            ['home', 'mission_1', 'html', 'Presentazione · paragrafo 1'],
            ['home', 'mission_2', 'html', 'Presentazione · paragrafo 2'],
            ['home', 'mission_3', 'html', 'Presentazione · paragrafo 3'],
            ['home', 'mission_4', 'html', 'Presentazione · paragrafo 4'],
            ['home', 'activities_image',            'image', 'Scopri le nostre attività · immagine di sfondo'],
            ['home', 'activity_projects_image',     'image', 'Scopri le nostre attività · card Progetti di ricerca'],
            ['home', 'activity_outreach_image',     'image', 'Scopri le nostre attività · card Divulgazione'],
            ['home', 'activity_publications_image', 'image', 'Scopri le nostre attività · card Pubblicazioni scientifiche'],

            // ─── Chi siamo ──────────────────────────────────────────────
            ['chi-siamo', 'hero_image',    'image', 'Testata · immagine'],
            ['chi-siamo', 'title',         'text',  'Testata · titolo (sul sito in maiuscolo)'],
            ['chi-siamo', 'history_1',     'html',  'Storia · paragrafo 1'],
            ['chi-siamo', 'history_2',     'html',  'Storia · paragrafo 2'],
            ['chi-siamo', 'history_3',     'html',  'Storia · paragrafo 3'],
            ['chi-siamo', 'board',         'html',  'Direttivo (attuale e anni passati)'],
            ['chi-siamo', 'team_image',     'image', 'Foto di gruppo'],
            ['chi-siamo', 'team_image_alt', 'text',  'Foto di gruppo · descrizione (per i lettori di schermo)'],
            ['chi-siamo', 'join_background', 'image', 'Diventa socio · immagine di sfondo della sezione'],
            ['chi-siamo', 'join_card_image', 'image', 'Diventa socio · immagine della card «Diventa socio»'],
            ['chi-siamo', 'join_title',      'text',  'Diventa socio › Introduzione · titolo'],
            ['chi-siamo', 'join_intro',      'html',  'Diventa socio › Introduzione · testo'],
            ['chi-siamo', 'join_card_front', 'image', 'Diventa socio › Tessera socio · fronte'],
            ['chi-siamo', 'join_card_back',  'image', 'Diventa socio › Tessera socio · retro'],
            ['chi-siamo', 'annual_title',     'text',  'Diventa socio › Iscrizione annuale · titolo'],
            ['chi-siamo', 'annual_text',      'html',  'Diventa socio › Iscrizione annuale · testo'],
            ['chi-siamo', 'annual_image',     'image', 'Diventa socio › Iscrizione annuale · foto'],
            ['chi-siamo', 'annual_image_alt', 'text',  'Diventa socio › Iscrizione annuale · descrizione della foto'],
            ['chi-siamo', 'meetings_title',     'text',  'Diventa socio › Riunioni mensili · titolo'],
            ['chi-siamo', 'meetings_text',      'html',  'Diventa socio › Riunioni mensili · testo'],
            ['chi-siamo', 'meetings_image',     'image', 'Diventa socio › Riunioni mensili · foto'],
            ['chi-siamo', 'meetings_image_alt', 'text',  'Diventa socio › Riunioni mensili · descrizione della foto'],
            ['chi-siamo', 'comms_title',     'text',  'Diventa socio › Comunicazioni tra i soci · titolo'],
            ['chi-siamo', 'comms_text',      'html',  'Diventa socio › Comunicazioni tra i soci · testo'],
            ['chi-siamo', 'comms_image',     'image', 'Diventa socio › Comunicazioni tra i soci · foto'],
            ['chi-siamo', 'comms_image_alt', 'text',  'Diventa socio › Comunicazioni tra i soci · descrizione della foto'],
            ['chi-siamo', 'donations_title', 'text',  'Diventa socio › Donazioni · titolo'],
            ['chi-siamo', 'donations_text',  'html',  'Diventa socio › Donazioni · testo'],
            ['chi-siamo', 'form_title',      'text',  'Diventa socio › Modulo di iscrizione · titolo'],
            ['chi-siamo', 'form_text',       'html',  'Diventa socio › Modulo di iscrizione · testo'],

            // ─── News ───────────────────────────────────────────────────
            ['news', 'hero_image', 'image', 'Testata · immagine'],
            ['news', 'title',      'text',  'Testata · titolo (sul sito in maiuscolo)'],
            ['news', 'intro',      'html',  'Testo introduttivo'],

            // ─── Progetti, Divulgazione, Pubblicazioni ──────────────────
            // I testi introduttivi: il primo valore arriva dai dati attuali
            // del sito (data/gio-data.json), poi si cambiano dal pannello
            ['progetti', 'hero_image', 'image', 'Testata · immagine'],
            ['progetti', 'title',      'text',  'Testata · titolo (sul sito in maiuscolo)'],
            ['progetti', 'intro',      'html',  'Testo introduttivo'],

            ['divulgazione', 'hero_image', 'image', 'Testata · immagine'],
            ['divulgazione', 'title',      'text',  'Testata · titolo (sul sito in maiuscolo)'],
            ['divulgazione', 'intro',      'html',  'Testo introduttivo'],

            ['pubblicazioni', 'hero_image', 'image', 'Testata · immagine'],
            ['pubblicazioni', 'title',      'text',  'Testata · titolo (sul sito in maiuscolo)'],
            ['pubblicazioni', 'intro',      'html',  'Testo introduttivo'],

            // ─── Social e link utili ────────────────────────────────────
            ['links', 'hero_image', 'image', 'Testata · immagine di sfondo'],
            ['links', 'title',      'text',  'Testata · titolo (sul sito in maiuscolo)'],
            ['links', 'panel_portali_image',        'image', 'Pannello «Portali di osservazioni e citizen science» · immagine'],
            ['links', 'panel_organizzazioni_image', 'image', 'Pannello «Organizzazioni internazionali e scientifiche» · immagine'],
            ['links', 'panel_reti_image',           'image', 'Pannello «Italia - reti, associazioni e gruppi regionali» · immagine'],
            ['links', 'panel_turismo_image',        'image', 'Pannello «Turismo naturalistico» · immagine'],
            ['links', 'social_image', 'image', 'Social e newsletter · immagine di sfondo'],
        ];

        // Valori iniziali, usati solo se il campo è ancora vuoto.
        // page-defaults.json contiene i testi attuali del sito (gli stessi
        // scritti come default nel frontend): così, aprendo un campo nel
        // pannello, l'admin trova il testo da modificare e non un campo vuoto.
        // Le immagini restano vuote: vale l'immagine del frontend.
        $data = json_decode(file_get_contents(__DIR__.'/data/gio-data.json'), true);
        $defaults = json_decode(file_get_contents(__DIR__.'/data/page-defaults.json'), true);
        $initial = [
            'pubblicazioni.intro' => $data['pubblicazioni']['intro_text'] ?? null,
            'progetti.intro' => $data['progetti']['intro_text'] ?? null,
            'divulgazione.intro' => $data['divulgazione']['intro_text'] ?? null,
        ] + $defaults;

        foreach ($fields as $sort => [$page, $key, $type, $label]) {
            $content = PageContent::firstOrNew(['page' => $page, 'key' => $key]);
            $content->fill(['type' => $type, 'label' => $label, 'sort' => $sort]);
            if ($content->value === null && isset($initial["{$page}.{$key}"])) {
                $content->value = $initial["{$page}.{$key}"];
            }
            $content->save();
        }
    }

    /** Le 4 card dello slider della Home (sotto la copertina). */
    private static function homeCards(): array
    {
        $rows = [];
        foreach (range(1, 4) as $n) {
            $rows[] = ['home', "card_{$n}_title",       'text',  "Card {$n} · titolo"];
            $rows[] = ['home', "card_{$n}_description", 'html',  "Card {$n} · testo"];
            $rows[] = ['home', "card_{$n}_text",        'text',  "Card {$n} · riga sopra la citazione (facoltativa)"];
            $rows[] = ['home', "card_{$n}_quote",       'html',  "Card {$n} · citazione (facoltativa)"];
            $rows[] = ['home', "card_{$n}_image",       'image', "Card {$n} · immagine (vuota = la stessa della copertina {$n})"];
        }

        return $rows;
    }
}
