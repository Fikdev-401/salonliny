<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KaryawanUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $roleKaryawan = Role::where('slug', 'karyawan')->first();
        $roleKasir = Role::where('slug', 'kasir')->first();
        
        if (!$roleKaryawan) {
            $this->command->error('Role karyawan tidak ditemukan. Jalankan RoleSeeder terlebih dahulu.');
            return;
        }

        // Data karyawan yang ada di sistem
        $karyawanData = [
            [
                'nama' => 'Ahmad Santoso',
                'username' => 'ahmad.santoso',
                'jabatan' => 'Tukang Cukur Senior',
                'password' => 'password123',
                'role' => 'karyawan'
            ],
            [
                'nama' => 'Budi Wijaya',
                'username' => 'budi.wijaya',
                'jabatan' => 'Tukang Cukur',
                'password' => 'password123',
                'role' => 'karyawan'
            ],
            [
                'nama' => 'Siti Aminah',
                'username' => 'siti.aminah',
                'jabatan' => 'Kapster',
                'password' => 'password123',
                'role' => 'karyawan'
            ],
            [
                'nama' => 'Dewi Kusuma',
                'username' => 'dewi.kusuma',
                'jabatan' => 'Kapster Senior',
                'password' => 'password123',
                'role' => 'karyawan'
            ],
            [
                'nama' => 'Rudi Hartono',
                'username' => 'rudi.hartono',
                'jabatan' => 'Kasir',
                'password' => 'password123',
                'role' => 'kasir' // Rudi sebagai Kasir
            ],
        ];

        foreach ($karyawanData as $data) {
            // Cari karyawan berdasarkan nama dan jabatan
            $karyawan = Karyawan::where('nama', $data['nama'])
                ->where('jabatan', $data['jabatan'])
                ->first();

            if (!$karyawan) {
                $this->command->warn("Karyawan {$data['nama']} ({$data['jabatan']}) tidak ditemukan di database.");
                continue;
            }

            // Cek apakah karyawan sudah punya user
            if ($karyawan->user_id) {
                $this->command->info("Karyawan {$data['nama']} sudah terhubung dengan user.");
                continue;
            }

            // Cek apakah user sudah ada
            $user = User::where('username', $data['username'])->first();

            if (!$user) {
                // Buat user baru
                $roleId = $data['role'] === 'kasir' ? $roleKasir->id : $roleKaryawan->id;
                
                $user = User::create([
                    'nama' => $data['nama'],
                    'username' => $data['username'],
                    'email' => $data['username'] . '@gasing.fun',
                    'password' => Hash::make($data['password']),
                    'role_id' => $roleId,
                    'status' => 'aktif',
                ]);

                $this->command->info("User {$data['username']} berhasil dibuat.");
            }

            // Hubungkan karyawan dengan user
            $karyawan->update(['user_id' => $user->id]);
            
            $this->command->info("✅ Karyawan {$data['nama']} berhasil dihubungkan dengan user {$data['username']}.");
        }

        $this->command->info("\n🎉 Seeder selesai!");
        $this->command->info("\n📋 Login untuk 5 karyawan:");
        $this->command->info("Username: ahmad.santoso / budi.wijaya / siti.aminah / dewi.kusuma / rudi.hartono");
        $this->command->info("Password: password123");
        $this->command->info("\n📝 Catatan: Rudi Hartono (rudi.hartono) sebagai Kasir.");
    }
}
