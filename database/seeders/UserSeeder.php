<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all()->keyBy('slug');

        // Super Admin
        User::create([
            'nama' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@salon.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['super-admin']->id,
            'no_hp' => '081234567890',
            'alamat' => 'Alamat Super Admin',
            'status' => 'aktif',
        ]);

        // Pimpinan
        User::create([
            'nama' => 'Pimpinan',
            'username' => 'pimpinan',
            'email' => 'pimpinan@salon.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['pimpinan']->id,
            'no_hp' => '081234567891',
            'alamat' => 'Alamat Pimpinan',
            'status' => 'aktif',
        ]);

        // Admin
        User::create([
            'nama' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@salon.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['admin']->id,
            'no_hp' => '081234567892',
            'alamat' => 'Alamat Admin',
            'status' => 'aktif',
        ]);

        // Kasir
        User::create([
            'nama' => 'Kasir',
            'username' => 'kasir',
            'email' => 'kasir@salon.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['kasir']->id,
            'no_hp' => '081234567893',
            'alamat' => 'Alamat Kasir',
            'status' => 'aktif',
        ]);

        // Karyawan
        User::create([
            'nama' => 'Karyawan',
            'username' => 'karyawan',
            'email' => 'karyawan@salon.com',
            'password' => Hash::make('password'),
            'role_id' => $roles['karyawan']->id,
            'no_hp' => '081234567894',
            'alamat' => 'Alamat Karyawan',
            'status' => 'aktif',
        ]);
    }
}
