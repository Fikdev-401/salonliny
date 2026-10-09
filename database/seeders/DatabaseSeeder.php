<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // For production: only seed essential data
        $this->call([
            ProductionSeeder::class,
        ]);
    }
}
