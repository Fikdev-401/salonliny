<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat karyawan dari user karyawan
        $userKaryawan = User::where('username', 'karyawan')->first();
        
        Karyawan::create([
            'nama' => 'Ahmad Santoso',
            'jabatan' => 'Tukang Cukur Senior',
            'tanggal_lahir' => '1990-05-15',
            'tanggal_bergabung' => '2020-01-10',
            'no_hp' => '081234567894',
            'alamat' => 'Jl. Mawar No. 123, Jakarta',
            'jenis_kelamin' => 'L',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 3500000,
            'user_id' => $userKaryawan?->id,
        ]);

        // Karyawan lain tanpa user
        Karyawan::create([
            'nama' => 'Budi Wijaya',
            'jabatan' => 'Tukang Cukur',
            'tanggal_lahir' => '1995-08-20',
            'tanggal_bergabung' => '2021-03-15',
            'no_hp' => '081234567895',
            'alamat' => 'Jl. Melati No. 45, Jakarta',
            'jenis_kelamin' => 'L',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 3000000,
            'user_id' => null,
        ]);

        Karyawan::create([
            'nama' => 'Siti Aminah',
            'jabatan' => 'Kapster',
            'tanggal_lahir' => '1992-11-10',
            'tanggal_bergabung' => '2020-06-01',
            'no_hp' => '081234567896',
            'alamat' => 'Jl. Anggrek No. 78, Jakarta',
            'jenis_kelamin' => 'P',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 3200000,
            'user_id' => null,
        ]);

        Karyawan::create([
            'nama' => 'Dewi Kusuma',
            'jabatan' => 'Kapster Senior',
            'tanggal_lahir' => '1988-03-25',
            'tanggal_bergabung' => '2019-02-10',
            'no_hp' => '081234567897',
            'alamat' => 'Jl. Kenanga No. 90, Jakarta',
            'jenis_kelamin' => 'P',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 3800000,
            'user_id' => null,
        ]);

        Karyawan::create([
            'nama' => 'Rudi Hartono',
            'jabatan' => 'Asisten',
            'tanggal_lahir' => '1998-07-30',
            'tanggal_bergabung' => '2023-01-05',
            'no_hp' => '081234567898',
            'alamat' => 'Jl. Cempaka No. 12, Jakarta',
            'jenis_kelamin' => 'L',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 2500000,
            'user_id' => null,
        ]);
    }
}
