# G.I.O. — Backend (Laravel 12 + Filament 4)

API pubbliche per il sito Nuxt e pannello di amministrazione per i contenuti
del Gruppo Insubrico di Ornitologia.

- **Pannello:** `/admin` (Filament, in italiano)
- **API:** `/api/...`, lette dal frontend Nuxt (`NUXT_PUBLIC_API_BASE`)
- Requisiti: **PHP 8.2+**, Composer, MySQL/MariaDB (o SQLite in locale)

## Avvio in locale

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed          # tabelle + contenuti attuali del sito
php artisan gio:import-files ../gruppo-insubrico-ornitologia-nuxt/public
php artisan storage:link
php artisan make:filament-user      # primo amministratore
php artisan serve                   # http://localhost:8000/admin
```

Con Sail: stessi comandi preceduti da `./vendor/bin/sail`.

Nel `.env` del frontend: `NUXT_PUBLIC_API_BASE=http://localhost:8000/api`.

## Cosa c'è

| Sezione del sito | Pannello | API |
| --- | --- | --- |
| News | Contenuti › News | `GET /api/news?page&perPage`, `GET /api/news/{slug}` |
| Pubblicazioni (Quaderni, BOL, Resoconti, Bibliografia…) | Contenuti › Pubblicazioni | `GET /api/pubblicazioni`, `GET /api/pubblicazioni/{percorso}` |
| Progetti | Progetti › Progetti, Sezioni, Galleria | `GET /api/progetti`, `/api/progetti/sections`, `/api/progetti/{sezione}/{progetto?}` |
| Divulgazione | Contenuti › Divulgazione | `GET /api/divulgazione`, `/api/divulgazione/subsections`, `/api/divulgazione/{slug}` |
| Social e link utili | Contenuti › Link utili | `GET /api/links` |
| Testi fissi delle pagine | Pagine › Testi delle pagine | `GET /api/pages/{pagina}` |
| Ricerca | — | `GET /api/search?q&limit` |
| Form di contatto | Messaggi › Form di contatto | `POST /api/contact` |
| Newsletter (Mailchimp) | — (si gestisce su Mailchimp) | `POST /api/newsletter/subscribe` |

Le risposte hanno la stessa forma dei dati finti usati finora nei composable
Nuxt. Unica aggiunta: `GET /api/pubblicazioni/{percorso}` restituisce anche
`crumbs` (la breadcrumb), che prima il frontend calcolava da solo.

## Comandi utili

- `php artisan db:seed --class=ContentSeeder` — carica i contenuti dai dati
  attuali (`database/seeders/data/gio-data.json`). Salta le sezioni che hanno
  già dati: non cancella niente di quanto inserito dal pannello.
- `php artisan db:seed --class=PageContentSeeder` — registra i testi
  modificabili delle pagine. Per renderne modificabile uno nuovo: aggiungi
  una riga nel seeder e rilancialo.
- `php artisan gio:import-files <cartella public del frontend> [--dry-run]` —
  copia PDF e immagini nello storage di Laravel e aggiorna i percorsi.
  Elenca i file che non trova.
- `php artisan make:filament-user` — crea un amministratore (poi si possono
  aggiungere dal pannello, Impostazioni › Amministratori).
- `php artisan test` — test di API, form e pannello.

## Percorsi dei file

Nei campi file si può trovare:

- `pubblicazioni/x.pdf` — file caricato dal pannello, servito da `/storage/…`
- `/documents/…` — file ancora nel frontend (prima di `gio:import-files`)
- `https://…` — link esterno

La conversione in URL è in `App\Support\FileUrl`.

## Messa online (in breve)

1. Document root del dominio (es. `api.gruppoinsubrico.com`) sulla cartella `public/`.
2. `composer install --no-dev --optimize-autoloader`
3. `.env` di produzione (mai su git):
   `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://api.gruppoinsubrico.com`,
   nuova `APP_KEY`, `DB_*`, `MAIL_*`, `CONTACT_MAIL_TO`,
   `MAILCHIMP_API_KEY`, `MAILCHIMP_LIST_ID`,
   `CORS_ALLOWED_ORIGINS=https://gruppoinsubrico.com,https://www.gruppoinsubrico.com`
4. `php artisan migrate --force --seed`, `php artisan gio:import-files …`,
   `php artisan storage:link`, `php artisan make:filament-user`
5. `php artisan optimize` e `php artisan filament:optimize`
6. PHP: `upload_max_filesize` e `post_max_size` almeno 64M (PDF fino a 50 MB).

Il pannello sta sullo stesso dominio dell'API: niente cookie tra domini
diversi. Il frontend legge solo le API pubbliche.

## Dal vecchio progetto (Laravel 10)

Questo progetto sostituisce il precedente `gruppo-insubrico-ornitologia-laravel`
(Laravel 10, non più supportato). Sono stati riportati: form di contatto
(controller, mail, viste), testi modificabili delle pagine, newsletter.
Cambiamenti:

- il pannello è Filament: le vecchie rotte `api/admin/page-contents` e
  Sanctum non servono più;
- `contact_messages.message` è ora `text` (prima `string`, 255 caratteri:
  i messaggi lunghi venivano troncati o rifiutati da MySQL);
- il destinatario del form di contatto si imposta con `CONTACT_MAIL_TO`;
- `.env.prod` e il file SQLite `laravel` non vanno più nel repository.
