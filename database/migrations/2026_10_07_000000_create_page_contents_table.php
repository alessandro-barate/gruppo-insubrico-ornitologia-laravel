<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Testi e immagini "fissi" delle pagine, modificabili dal pannello admin.
     * Ogni riga è un singolo campo: es. page=news, key=intro.
     * value = null significa "usa il default scritto nel frontend".
     */
    public function up(): void
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page', 100);          // es. "home", "news", "chi-siamo"
            $table->string('key', 100);           // es. "title", "intro", "hero_image"
            $table->enum('type', ['text', 'html', 'image'])->default('text');
            $table->string('label');              // etichetta leggibile mostrata nel pannello
            $table->text('value')->nullable();    // testo, HTML o percorso immagine
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['page', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_contents');
    }
};
