<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabelle dei contenuti del sito, gestiti dal pannello Filament.
 * La forma dei dati restituiti dalle API (app/Http/Controllers/Api) è la
 * stessa dei dati finti usati finora nei composable del frontend Nuxt.
 *
 * Nei campi "file" / "image_path" si salva:
 *  - un percorso relativo al disco "public" per i file caricati dal pannello
 *    (es. "pubblicazioni/bol-v1-n1.pdf")
 *  - oppure un percorso che inizia con "/" per i file ancora serviti dal
 *    frontend (es. "/documents/progetti/2018-goosander.pdf"), finché non
 *    vengono importati con `php artisan gio:import-files`
 *  - oppure un URL completo (https://…)
 * Vedi App\Support\FileUrl.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─── News ──────────────────────────────────────────
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category')->nullable();
            $table->date('date');
            $table->string('image')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            // [{ type: pdf|word|excel|powerpoint|external, label, file?, url? }]
            $table->json('attachments')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // ─── Pubblicazioni ─────────────────────────────────
        // Albero di sezioni: ogni sezione ha un tipo (group = contenitore
        // di altre sezioni; detail = pagina singola, es. un Quaderno;
        // pdf-list / bibliography / cards = lista di voci).
        Schema::create('publication_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()
                ->constrained('publication_sections')->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->string('type', 20); // group | detail | pdf-list | bibliography | cards
            $table->string('intro_excerpt')->nullable();
            $table->longText('intro_text')->nullable();
            $table->string('second_intro_text')->nullable();
            $table->string('image_path')->nullable();   // immagine della card
            $table->string('header_image')->nullable(); // immagine in testa alla pagina
            $table->unsignedSmallInteger('items_per_page')->nullable();
            $table->boolean('group_by_year')->default(false); // pdf-list
            // solo "detail"
            $table->longText('body')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('price')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['parent_id', 'slug']);
        });

        // Voci delle sezioni-lista. I campi usati dipendono dal tipo di
        // sezione: pdf-list → title, year, file_path; bibliography → text,
        // file_path; cards → title, text, image_path.
        Schema::create('publication_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_section_id')->constrained()->cascadeOnDelete();
            $table->string('title', 500)->nullable();
            $table->longText('text')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('image_path')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Pagine interne di un Quaderno ("a libro aperto")
        Schema::create('publication_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_section_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // ─── Progetti ──────────────────────────────────────
        Schema::create('project_sections', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('intro_excerpt')->nullable();
            $table->longText('intro_text')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('has_projects')->default(true);
            // true = ogni progetto ha una sua pagina; false = progetti in lista
            $table->boolean('projects_are_navigable')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            // Cambiare la sezione sposta il progetto (es. da "in corso" a "terminati")
            $table->foreignId('project_section_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->string('image_path')->nullable();
            $table->longText('intro')->nullable();
            // [{ text, links: [{ kind, label, file?, url? }] }]
            $table->json('blocks')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['project_section_id', 'slug']);
        });

        // Galleria della pagina Progetti (riusabile per altre pagine)
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('gallery', 50)->default('progetti');
            $table->string('image_path');
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // ─── Divulgazione ──────────────────────────────────
        Schema::create('outreach_sections', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->json('title_parts')->nullable(); // righe del titolo nella card
            $table->string('aria_label')->nullable();
            $table->string('intro_excerpt')->nullable();
            $table->longText('intro_text')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('outreach_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outreach_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('year', 10)->nullable();
            $table->longText('body')->nullable();
            $table->string('image_path')->nullable();
            // [{ label, file?, url? }]
            $table->json('links')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // ─── Link utili ────────────────────────────────────
        // Una riga = una colonna di un pannello (categoria) della pagina
        // Social e link utili, con i suoi gruppi di link.
        Schema::create('link_subsections', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30); // portali | organizzazioni | reti | turismo
            $table->string('title');
            // [{ title?, links: [{ title, subtitle?, href }] }]
            $table->json('groups')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_subsections');
        Schema::dropIfExists('outreach_items');
        Schema::dropIfExists('outreach_sections');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_sections');
        Schema::dropIfExists('publication_pages');
        Schema::dropIfExists('publication_items');
        Schema::dropIfExists('publication_sections');
        Schema::dropIfExists('news');
    }
};
