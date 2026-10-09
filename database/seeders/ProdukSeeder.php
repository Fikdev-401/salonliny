<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produks = [
            // Produk dengan harga tetap
            [
                'nama' => 'Pomade Waterbased',
                'kode' => 'PRD-001',
                'deskripsi' => 'Pomade berbasis air',
                'kategori' => 'Styling',
                'satuan' => 'pcs',
                'stok' => 20,
                'stok_minimal' => 5,
                'harga_jual' => 85000,
                'harga_beli' => 50000,
                'harga_fleksibel' => false,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Pomade Oilbased',
                'kode' => 'PRD-002',
                'deskripsi' => 'Pomade berbasis minyak',
                'kategori' => 'Styling',
                'satuan' => 'pcs',
                'stok' => 15,
                'stok_minimal' => 5,
                'harga_jual' => 75000,
                'harga_beli' => 45000,
                'harga_fleksibel' => false,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Tonic',
                'kode' => 'PRD-003',
                'deskripsi' => 'Tonik perawatan rambut',
                'kategori' => 'Perawatan',
                'satuan' => 'pcs',
                'stok' => 25,
                'stok_minimal' => 5,
                'harga_jual' => 65000,
                'harga_beli' => 40000,
                'harga_fleksibel' => false,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Vitamin',
                'kode' => 'PRD-004',
                'deskripsi' => 'Vitamin rambut',
                'kategori' => 'Perawatan',
                'satuan' => 'pcs',
                'stok' => 30,
                'stok_minimal' => 5,
                'harga_jual' => 55000,
                'harga_beli' => 35000,
                'harga_fleksibel' => false,
                'status' => 'aktif',
            ],
            [
                'nama' => 'After Shave',
                'kode' => 'PRD-005',
                'deskripsi' => 'Perawatan pasca cukur',
                'kategori' => 'Perawatan',
                'satuan' => 'pcs',
                'stok' => 18,
                'stok_minimal' => 5,
                'harga_jual' => 45000,
                'harga_beli' => 28000,
                'harga_fleksibel' => false,
                'status' => 'aktif',
            ],
            // Bahan operasional
            [
                'nama' => 'Shampoo Premium',
                'kode' => 'BHN-001',
                'deskripsi' => 'Shampoo untuk cuci rambut',
                'kategori' => 'Bahan',
                'satuan' => 'botol',
                'stok' => 10,
                'stok_minimal' => 3,
                'harga_jual' => 0,
                'harga_beli' => 75000,
                'harga_fleksibel' => true,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Conditioner',
                'kode' => 'BHN-002',
                'deskripsi' => 'Conditioner untuk perawatan',
                'kategori' => 'Bahan',
                'satuan' => 'botol',
                'stok' => 8,
                'stok_minimal' => 3,
                'harga_jual' => 0,
                'harga_beli' => 65000,
                'harga_fleksibel' => true,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Cream Treatment',
                'kode' => 'BHN-003',
                'deskripsi' => 'Cream untuk treatment rambut',
                'kategori' => 'Bahan',
                'satuan' => 'pcs',
                'stok' => 12,
                'stok_minimal' => 5,
                'harga_jual' => 0,
                'harga_beli' => 45000,
                'harga_fleksibel' => true,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Pewarna Rambut',
                'kode' => 'BHN-004',
                'deskripsi' => 'Pewarna untuk coloring',
                'kategori' => 'Bahan',
                'satuan' => 'pcs',
                'stok' => 20,
                'stok_minimal' => 5,
                'harga_jual' => 0,
                'harga_beli' => 35000,
                'harga_fleksibel' => true,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Handuk Sekali Pakai',
                'kode' => 'BHN-005',
                'deskripsi' => 'Handuk untuk pelanggan',
                'kategori' => 'Bahan',
                'satuan' => 'pak',
                'stok' => 50,
                'stok_minimal' => 10,
                'harga_jual' => 0,
                'harga_beli' => 25000,
                'harga_fleksibel' => false,
                'status' => 'aktif',
            ],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}
