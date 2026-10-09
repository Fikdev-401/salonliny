<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nama' => 'Super Admin',
                'slug' => 'super-admin',
                'deskripsi' => 'Full access ke seluruh sistem',
            ],
            [
                'nama' => 'Pimpinan',
                'slug' => 'pimpinan',
                'deskripsi' => 'Melihat laporan dan memantau usaha',
            ],
            [
                'nama' => 'Admin',
                'slug' => 'admin',
                'deskripsi' => 'Mengelola data utama sistem',
            ],
            [
                'nama' => 'Kasir',
                'slug' => 'kasir',
                'deskripsi' => 'Mencatat transaksi dan pembayaran',
            ],
            [
                'nama' => 'Karyawan',
                'slug' => 'karyawan',
                'deskripsi' => 'Akses kebutuhan operasional',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
