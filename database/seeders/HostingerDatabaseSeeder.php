<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HostingerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Hostinger deployment.
     */
    public function run(): void
    {
        // Jalankan seeder yang sama dengan sebelumnya
        // Data awal untuk Hostinger
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            // Data master awal (bisa dikosongkan jika tidak diperlukan)
            KaryawanSeeder::class,
            PelangganSeeder::class,
            LayananSeeder::class,
            ProdukSeeder::class,
        ]);
    }
}
