<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trasparenza sui contributi pubblici (Legge 124/2017): per ogni anno in
 * cui i contributi ricevuti superano i 10.000 €, il PDF con l'elenco.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->string('file_path');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_contributions');
    }
};
