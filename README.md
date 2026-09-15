<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[WebReinvent](https://webreinvent.com/)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Jump24](https://jump24.co.uk)**
-   **[Redberry](https://redberry.international/laravel/)**
-   **[Active Logic](https://activelogic.com)**
-   **[byte5](https://byte5.de)**
-   **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Laravel Template
Questa repo template contiene una versione modificata del pacchetto laravel/laravel. Tale versione differisce nei seguenti punti:

-   PostCSS è stato rimosso
-   È stato installato SASS
-   È stato installato Bootstrap
-   La cartella resources/css è stata rimossa
-   È stata aggiunta la cartella resources/scss contenente il file app.scss
-   Il file vite.config.js è stato modificato al fine di includere i file resources/scss/app.scss e resources/js/app.js nella compilazione. Sono stati inoltre aggiunti gli alias per le cartelle /resources/ e node_modules/bootstrap

Nella view welcome:

-   Sono stati inclusi gli asset tramite direttiva @vite
-   È stato rimosso lo stile preesistente
-   È stato modificato il contenuto
-   Passi da effettuare per RIPRODURRE questo template
-   Eseguire il comando npm remove postcss per rimuovere PostCSS
-   Eseguire il comando npm i per installare tutti i pacchetti di NPM (comprese le versioni aggiornate di vite e laravel-vite-plugin)
-   Eseguire il comando npm i --save-dev sass per installare SASS
-   Rinominare la cartella css che si trova nella cartella resources/ in scss
-   Rinominare il file app.css che si trova nella nuova cartella resources/scss in app.scss
-   Modificare il file vite.config.js:
-   Cambiare la sezione laravel(...) in:
    laravel({
    input: [
    'resources/scss/app.scss',
    'resources/js/app.js'
    ],
    refresh: true
    })
    per includere i file resources/scss/app.scss e resources/js/app.js nella compilazione (npm run dev/build)
-   Aggiungere la sezione:
    resolve: {
    alias: {
    '~resources': '/resources/',
    }
    },
    dopo la sezione plugins: [...] per creare un alias della cartella /resources/ (evitandoci di doverla richiamare in questo modo tutte le volte)
-   Aggiungere la riga import '~resources/scss/app.scss'; nel file resources/js/app.js per importare tramite JS il file SCSS principale
-   Aggiungere la direttiva @vite('resources/js/app.js') nella sezione <head> del file resources/views/welcome.blade.php per includere gli asset nella view
-   Aggiungere le righe:
    import.meta.glob([
    '../img/**'
    ]);
    nel file resources/js/app.js per istruire Vite e Blade affinché processino correttamente i nostri asset
-   Aggiungere la riga package-lock.json nel file .gitignore che si trova nella root del progetto per evitare di pubblicarlo nella repo (è un file che viene generato ed aggiornato automaticamente dopo l'esecuzione del comando npm i)

Installare Bootstrap:

-   Eseguire il comando npm i --save bootstrap @popperjs/core per installare sia la parte CSS che la parte JS di Bootstrap
-   Aggiungere la riga import path from path nel file vite.config.js
-   Aggiungere la riga '~bootstrap': path.resolve(\_\_dirname, 'node_modules/bootstrap') nell'oggetto resolve.alias nel file vite.config.js
-   Aggiungere la riga @import "~bootstrap/scss/bootstrap"; in alto nel file resources/scss/app.scss per importare la parte CSS di Bootstrap
-   Aggiungere la riga import \* as bootstrap from 'bootstrap'; nel file resources/js/app.js per importare la parte JS di Bootstrap
-   Passi da effettuare per UTILIZZARE questo template
-   Aprire questa repository su github e cliccare sul pulsante Use this template > Create a new repository
-   Clonare la repository appena creata su VS Code
-   Aprire il terminale
-   Copiare il file .env.example e rinominarlo in .env
-   Eseguire il comando composer install
-   Eseguire il comando php artisan key:generate
-   Eseguire il comando npm i o npm install
-   Aprire un secondo terminale
-   In uno dei due terminali, eseguire il comando php artisan serve. Nell'altro, npm run dev

Creare una template repository

-   Aprire la repo su github
-   Cliccare su Settings
-   Spuntare la casella Template repository
