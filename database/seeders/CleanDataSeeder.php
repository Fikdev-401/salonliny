<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membersihkan semua data kecuali akun user
     */
    public function run(): void
    {
        // Hapus data transaksi dan detailnya
        DB::table('transaksi_details')->delete();
        DB::table('transaksis')->delete();
        
        // Hapus data pembelian
        DB::table('pembelians')->delete();
        
        // Hapus data pengeluaran
        DB::table('pengeluarans')->delete();
        
        // Hapus data absensi
        DB::table('absensis')->delete();
        
        // Hapus data gaji
        DB::table('gajis')->delete();
        
        // Hapus data pelanggan
        DB::table('pelanggans')->delete();
        
        // Hapus data karyawan
        DB::table('karyawans')->delete();
        
        // Hapus data layanan
        DB::table('layanans')->delete();
        
        // Hapus data produk
        DB::table('produks')->delete();
        
        // Reset konfigurasi ke default
        DB::table('konfigurasis')->delete();
        DB::table('konfigurasis')->insert([
            'nama_perusahaan' => 'Salon & Barber Shop',
            'alamat' => null,
            'no_telepon' => null,
            'email' => null,
            'logo' => null,
            'jam_buka' => null,
            'jam_tutup' => null,
            'pajak_persen' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->command->info('Semua data telah dibersihkan kecuali akun user!');
    }
}
