<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * News in Home: show_on_home sceglie se la news compare nel carosello della
 * Home; home_until (facoltativa) la toglie dalla Home da sola dopo quella
 * data. Le news tolte dalla Home restano nella pagina News.
 * Le news già presenti restano in Home, come oggi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('show_on_home')->default(true)->after('is_published');
            $table->date('home_until')->nullable()->after('show_on_home');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['show_on_home', 'home_until']);
        });
    }
};
