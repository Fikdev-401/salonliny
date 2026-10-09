<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggans = [
            [
                'nama' => 'Andi Pratama',
                'no_hp' => '081298765432',
                'tanggal_lahir' => '1995-03-15',
                'alamat' => 'Jl. Sudirman No. 123',
                'jenis_kelamin' => 'L',
                'total_kunjungan' => 5,
                'terakhir_kunjungan' => '2026-03-10',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Budi Santoso',
                'no_hp' => '081298765433',
                'tanggal_lahir' => '1990-07-20',
                'alamat' => 'Jl. Thamrin No. 45',
                'jenis_kelamin' => 'L',
                'total_kunjungan' => 12,
                'terakhir_kunjungan' => '2026-03-12',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Citra Lestari',
                'no_hp' => '081298765434',
                'tanggal_lahir' => '1998-11-08',
                'alamat' => 'Jl. Gatot Subroto No. 78',
                'jenis_kelamin' => 'P',
                'total_kunjungan' => 8,
                'terakhir_kunjungan' => '2026-03-08',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Diana Wulandari',
                'no_hp' => '081298765435',
                'tanggal_lahir' => '1993-05-25',
                'alamat' => 'Jl. Rasuna Said No. 90',
                'jenis_kelamin' => 'P',
                'total_kunjungan' => 15,
                'terakhir_kunjungan' => '2026-03-14',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Eko Susanto',
                'no_hp' => '081298765436',
                'tanggal_lahir' => '1988-09-12',
                'alamat' => 'Jl. MH Thamrin No. 12',
                'jenis_kelamin' => 'L',
                'total_kunjungan' => 3,
                'terakhir_kunjungan' => '2026-02-28',
                'status' => 'aktif',
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}
