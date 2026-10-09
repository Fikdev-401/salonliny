<?php

namespace Database\Seeders;

use App\Models\Cabang;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Running Production Seeder...');

        $this->command->info('Creating roles...');
        $roles = [
            ['nama' => 'Super Admin', 'slug' => 'super-admin', 'deskripsi' => 'Full access to all features'],
            ['nama' => 'Admin', 'slug' => 'admin', 'deskripsi' => 'Manage daily operations'],
            ['nama' => 'Pimpinan', 'slug' => 'pimpinan', 'deskripsi' => 'View reports and approve'],
            ['nama' => 'Kasir', 'slug' => 'kasir', 'deskripsi' => 'Handle transactions'],
            ['nama' => 'Karyawan', 'slug' => 'karyawan', 'deskripsi' => 'Staff member'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }

        $this->command->info('  OK Roles created');

        $this->command->info('Creating default cabang...');
        $defaultCabang = Cabang::firstOrCreate(
            ['is_default' => true],
            [
                'nama' => 'Cabang Utama',
                'alamat' => 'Alamat Cabang Utama',
                'no_telepon' => null,
                'email' => null,
                'status' => 'aktif',
                'is_default' => true,
            ]
        );

        $this->command->info('  OK Default cabang created');

        $this->command->info('Creating super admin...');
        $superAdminRole = Role::where('slug', 'super-admin')->firstOrFail();
        $temporaryPassword = env('SUPER_ADMIN_PASSWORD') ?: Str::password(16);

        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nama' => 'Super Administrator',
                'email' => 'superadmin@salonbarber.local',
                'password' => Hash::make($temporaryPassword),
                'role_id' => $superAdminRole->id,
                'cabang_id' => $defaultCabang->id,
                'no_hp' => null,
                'alamat' => null,
                'status' => 'aktif',
                'must_change_password' => true,
            ]
        );

        $this->command->info('  OK Super Admin created: superadmin / ' . $temporaryPassword);
        $this->command->newLine();
        $this->command->info('Production seeding completed.');
        $this->command->info('Temporary login is shown once above. User must change password after login.');
    }
}
