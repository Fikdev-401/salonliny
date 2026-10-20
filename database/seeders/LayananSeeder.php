<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Kategori:
     * - Potong & Styling
     * - Warna & Chemical
     * - Perawatan / Treatment
     * - Wajah & Kecantikan
     * - Tubuh & Kulit
     *
     * Layanan bertanda "ke atas" menggunakan harga_fleksibel = true
     * (harga bisa lebih dari harga_default, biasanya menyesuaikan
     * panjang rambut, bahan, atau kompleksitas).
     */
    public function run(): void
    {
        $layanans = [
            // ============================================================
            // 1. RAMBUT - POTONG & STYLING
            // ============================================================
            [
                'nama' => 'Potong Rambut',
                'deskripsi' => 'Potong rambut standar untuk semua gaya',
                'harga_fleksibel' => false,
                'harga_default' => 50000,
                'insentif_karyawan' => 15000,
                'durasi_menit' => 30,
                'kategori' => 'Potong & Styling',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Blow Dry & Styling',
                'deskripsi' => 'Blow dan penataan rambut',
                'harga_fleksibel' => false,
                'harga_default' => 50000,
                'insentif_karyawan' => 15000,
                'durasi_menit' => 30,
                'kategori' => 'Potong & Styling',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hairdo / Sanggul',
                'deskripsi' => 'Sanggul untuk wisuda, pesta, dan pengantin',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 30000,
                'durasi_menit' => 60,
                'kategori' => 'Potong & Styling',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Catok',
                'deskripsi' => 'Pelurusan rambut dengan catok',
                'harga_fleksibel' => false,
                'harga_default' => 50000,
                'insentif_karyawan' => 15000,
                'durasi_menit' => 30,
                'kategori' => 'Potong & Styling',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Curling',
                'deskripsi' => 'Pengeritingan rambut dengan catok curling',
                'harga_fleksibel' => false,
                'harga_default' => 70000,
                'insentif_karyawan' => 20000,
                'durasi_menit' => 45,
                'kategori' => 'Potong & Styling',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Cuci Rambut + Blow / Catok',
                'deskripsi' => 'Cuci rambut dilanjutkan dengan blow atau catok',
                'harga_fleksibel' => false,
                'harga_default' => 90000,
                'insentif_karyawan' => 25000,
                'durasi_menit' => 45,
                'kategori' => 'Potong & Styling',
                'status' => 'aktif',
            ],

            // ============================================================
            // 2. RAMBUT - WARNA & CHEMICAL
            // ============================================================
            [
                'nama' => 'Semir / Pewarnaan Rambut',
                'deskripsi' => 'Full color, highlight, balayage, ombre. Harga bervariasi sesuai teknik dan panjang rambut.',
                'harga_fleksibel' => true,
                'harga_default' => 150000,
                'insentif_karyawan' => 40000,
                'durasi_menit' => 90,
                'kategori' => 'Warna & Chemical',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Smoothing / Rebonding',
                'deskripsi' => 'Pelurusan rambut permanen. Harga menyesuaikan panjang dan kondisi rambut.',
                'harga_fleksibel' => true,
                'harga_default' => 300000,
                'insentif_karyawan' => 75000,
                'durasi_menit' => 180,
                'kategori' => 'Warna & Chemical',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Keratin Treatment',
                'deskripsi' => 'Treatment keratin premium untuk rambut halus dan mudah diatur.',
                'harga_fleksibel' => true,
                'harga_default' => 500000,
                'insentif_karyawan' => 120000,
                'durasi_menit' => 180,
                'kategori' => 'Warna & Chemical',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Perming / Keriting',
                'deskripsi' => 'Pengeritingan rambut permanen. Harga menyesuaikan panjang rambut.',
                'harga_fleksibel' => true,
                'harga_default' => 500000,
                'insentif_karyawan' => 120000,
                'durasi_menit' => 180,
                'kategori' => 'Warna & Chemical',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Bleaching Rambut',
                'deskripsi' => 'Pemutih rambut untuk persiapan warna. Harga menyesuaikan ketebalan dan panjang.',
                'harga_fleksibel' => true,
                'harga_default' => 300000,
                'insentif_karyawan' => 70000,
                'durasi_menit' => 150,
                'kategori' => 'Warna & Chemical',
                'status' => 'aktif',
            ],

            // ============================================================
            // 3. RAMBUT - PERAWATAN / TREATMENT
            // ============================================================
            [
                'nama' => 'Creambath',
                'deskripsi' => 'Creambath klasik untuk kelembapan rambut',
                'harga_fleksibel' => false,
                'harga_default' => 120000,
                'insentif_karyawan' => 30000,
                'durasi_menit' => 60,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Spa',
                'deskripsi' => 'Spa rambut lengkap dengan pijat kepala',
                'harga_fleksibel' => false,
                'harga_default' => 150000,
                'insentif_karyawan' => 40000,
                'durasi_menit' => 75,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Mask',
                'deskripsi' => 'Perawatan masker rambut intensif',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 25000,
                'durasi_menit' => 45,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Hair Tonic Treatment (Anti Rontok)',
                'deskripsi' => 'Perawatan khusus untuk mengurangi kerontokan',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 25000,
                'durasi_menit' => 45,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Scalp Treatment / Anti Ketombe',
                'deskripsi' => 'Perawatan kulit kepala untuk mengatasi ketombe',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 25000,
                'durasi_menit' => 45,
                'kategori' => 'Perawatan',
                'status' => 'aktif',
            ],

            // ============================================================
            // 4. WAJAH & KECANTIKAN
            // ============================================================
            [
                'nama' => 'Facial Wajah',
                'deskripsi' => 'Facial basic, acne, whitening, atau anti-aging. Harga menyesuaikan jenis treatment.',
                'harga_fleksibel' => true,
                'harga_default' => 300000,
                'insentif_karyawan' => 70000,
                'durasi_menit' => 75,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Totok Wajah',
                'deskripsi' => 'Totok wajah untuk relaksasi dan kecantikan',
                'harga_fleksibel' => false,
                'harga_default' => 300000,
                'insentif_karyawan' => 70000,
                'durasi_menit' => 60,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Eyebrow Shaping',
                'deskripsi' => 'Penataan bentuk alis profesional',
                'harga_fleksibel' => false,
                'harga_default' => 50000,
                'insentif_karyawan' => 15000,
                'durasi_menit' => 20,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Cukur Alis',
                'deskripsi' => 'Cukur alis untuk tampilan rapi',
                'harga_fleksibel' => false,
                'harga_default' => 50000,
                'insentif_karyawan' => 15000,
                'durasi_menit' => 15,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Eyelash Extension',
                'deskripsi' => 'Tanam / extension bulu mata untuk tampilan natural hingga dramatis',
                'harga_fleksibel' => false,
                'harga_default' => 300000,
                'insentif_karyawan' => 80000,
                'durasi_menit' => 90,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Sulam Alis / Bibir / Eyeliner',
                'deskripsi' => 'Sulam permanen alis, bibir, atau eyeliner. Harga menyesuaikan area dan detail.',
                'harga_fleksibel' => true,
                'harga_default' => 2000000,
                'insentif_karyawan' => 400000,
                'durasi_menit' => 120,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Makeup Wisuda',
                'deskripsi' => 'Makeup khusus wisuda, tahan lama untuk foto dan acara',
                'harga_fleksibel' => false,
                'harga_default' => 300000,
                'insentif_karyawan' => 80000,
                'durasi_menit' => 60,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Makeup Pesta',
                'deskripsi' => 'Makeup untuk berbagai acara pesta',
                'harga_fleksibel' => false,
                'harga_default' => 200000,
                'insentif_karyawan' => 60000,
                'durasi_menit' => 45,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Makeup Pengantin',
                'deskripsi' => 'Makeup pengantin premium. Paket bisa include akad dan resepsi.',
                'harga_fleksibel' => true,
                'harga_default' => 1000000,
                'insentif_karyawan' => 250000,
                'durasi_menit' => 120,
                'kategori' => 'Wajah & Kecantikan',
                'status' => 'aktif',
            ],

            // ============================================================
            // 5. TUBUH & KULIT
            // ============================================================
            [
                'nama' => 'Lulur / Body Scrub',
                'deskripsi' => 'Lulur tubuh untuk mengangkat sel kulit mati',
                'harga_fleksibel' => false,
                'harga_default' => 200000,
                'insentif_karyawan' => 50000,
                'durasi_menit' => 60,
                'kategori' => 'Tubuh & Kulit',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Body Massage / Pijat',
                'deskripsi' => 'Pijat tubuh relaksasi, tarif per jam',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 30000,
                'durasi_menit' => 60,
                'kategori' => 'Tubuh & Kulit',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Sauna / Steam',
                'deskripsi' => 'Sauna atau steam untuk detoksifikasi tubuh, tarif per jam',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 25000,
                'durasi_menit' => 60,
                'kategori' => 'Tubuh & Kulit',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Waxing',
                'deskripsi' => 'Waxing kaki, tangan, atau ketiak. Harga menyesuaikan area.',
                'harga_fleksibel' => false,
                'harga_default' => 150000,
                'insentif_karyawan' => 40000,
                'durasi_menit' => 45,
                'kategori' => 'Tubuh & Kulit',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Bleaching Badan',
                'deskripsi' => 'Bleaching untuk pencerahan kulit tubuh',
                'harga_fleksibel' => false,
                'harga_default' => 200000,
                'insentif_karyawan' => 50000,
                'durasi_menit' => 45,
                'kategori' => 'Tubuh & Kulit',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Manicure & Pedicure',
                'deskripsi' => 'Perawatan kuku tangan dan kaki',
                'harga_fleksibel' => false,
                'harga_default' => 100000,
                'insentif_karyawan' => 30000,
                'durasi_menit' => 60,
                'kategori' => 'Tubuh & Kulit',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Nail Art / Extension Kuku / Gel Polish',
                'deskripsi' => 'Nail art, extension kuku, atau gel polish. Harga bervariasi sesuai desain.',
                'harga_fleksibel' => true,
                'harga_default' => 200000,
                'insentif_karyawan' => 50000,
                'durasi_menit' => 90,
                'kategori' => 'Tubuh & Kulit',
                'status' => 'aktif',
            ],
        ];

        foreach ($layanans as $layanan) {
            Layanan::updateOrCreate(
                ['nama' => $layanan['nama']],
                $layanan
            );
        }

        $this->command->info('✓ ' . count($layanans) . ' layanan berhasil di-seed.');
    }
}
