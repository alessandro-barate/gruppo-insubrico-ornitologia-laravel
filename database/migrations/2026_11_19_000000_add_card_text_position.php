<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Posizione del titolo sulle card con foto (Progetti, Divulgazione,
 * Pubblicazioni): "center" (al centro) o "bottom" (in basso).
 * Mantiene le posizioni di oggi: in basso nelle Pubblicazioni, al centro
 * nelle altre.
 */
return new class extends Migration
{
    private const TABLES = [
        'project_sections' => 'center',
        'projects' => 'center',
        'outreach_sections' => 'center',
        'publication_sections' => 'bottom',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table => $default) {
            Schema::table($table, function (Blueprint $t) use ($default) {
                $t->string('card_text_position', 10)->default($default);
            });
        }
    }

    public function down(): void
    {
        foreach (array_keys(self::TABLES) as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('card_text_position');
            });
        }
    }
};
