<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanans = [
            // Layanan dengan harga tetap
            [
                'nama' => 'Potong Rambut',
                'deskripsi' => 'Potong rambut standar',
                'harga_fleksibel' => false,
                'harga_default' => 50000,
                'insentif_karyawan' => 15000,
                'durasi_menit' => 30,
                'kategori' => 'Potong',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Shaving',
                'deskripsi' => 'Cukur jenggot dan kumis',
                'harga_fleksibel' => false,
                'harga_default' => 35000,
                'insentif_karyawan' => 10000,
                'durasi_menit' => 20,
                'kategori' => 'Cukur',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Creambath',
                'deskripsi' => 'Perawatan rambut creambath',
                'harga_fleksibel' => false,
                'harga_default' => 75000,
                'insentif_karyawan' => 20000,
                'durasi_menit' => 45,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Coloring - Basic',
                'deskripsi' => 'Pewarnaan rambut basic',
                'harga_fleksibel' => false,
                'harga_default' => 150000,
                'insentif_karyawan' => 40000,
                'durasi_menit' => 90,
                'kategori' => 'Coloring',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Facial',
                'deskripsi' => 'Perawatan wajah',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 25000,
                'durasi_menit' => 60,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],
            // Layanan dengan harga fleksibel
            [
                'nama' => 'Hair Coloring - Premium',
                'deskripsi' => 'Pewarnaan rambut premium dengan bahan berkualitas',
                'harga_fleksibel' => true,
                'harga_default' => 250000,
                'insentif_karyawan' => 60000,
                'durasi_menit' => 120,
                'kategori' => 'Coloring',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Smoothing / Pelurusan Rambut',
                'deskripsi' => 'Pelurusan rambut, harga menyesuaikan panjang dan kondisi rambut',
                'harga_fleksibel' => true,
                'harga_default' => 350000,
                'insentif_karyawan' => 80000,
                'durasi_menit' => 180,
                'kategori' => 'Treatment',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Keratin Treatment',
                'deskripsi' => 'Treatment keratin, harga menyesuaikan panjang rambut',
                'harga_fleksibel' => true,
                'harga_default' => 400000,
                'insentif_karyawan' => 100000,
                'durasi_menit' => 150,
                'kategori' => 'Treatment',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Extension',
                'deskripsi' => 'Penambahan hair extension',
                'harga_fleksibel' => true,
                'harga_default' => 500000,
                'insentif_karyawan' => 120000,
                'durasi_menit' => 180,
                'kategori' => 'Treatment',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Spa',
                'deskripsi' => 'Spa rambut lengkap',
                'harga_fleksibel' => false,
                'harga_default' => 120000,
                'insentif_karyawan' => 30000,
                'durasi_menit' => 75,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],
        ];

        foreach ($layanans as $layanan) {
            Layanan::create($layanan);
        }
    }
}
