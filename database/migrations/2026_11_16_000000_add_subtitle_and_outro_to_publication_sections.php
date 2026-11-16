<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pubblicazioni:
 * - subtitle: sottotitolo sotto il titolo delle pagine singole (Quaderni);
 * - outro_text: testo o foto in fondo alla pagina, dopo l'elenco.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication_sections', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
            $table->text('outro_text')->nullable()->after('second_intro_text');
        });
    }

    public function down(): void
    {
        Schema::table('publication_sections', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'outro_text']);
        });
    }
};
