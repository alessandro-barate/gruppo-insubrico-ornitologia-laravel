<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed
     * Campi modificabili delle pagine + contenuti attuali del sito.
     * Gli account admin si creano con: php artisan make:filament-user
     */
    public function run(): void
    {
        $this->call([
            PageContentSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
