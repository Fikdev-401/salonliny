<?php

namespace Tests\Feature\Routes;

use App\Models\Gaji;
use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class RouteRegressionTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_routes_that_previously_returned_500_are_now_accessible(): void
    {
        $admin = $this->createUserWithRole('admin');
        $karyawan = $this->actingAs($admin)->createKaryawan();
        $layanan = $this->createLayanan();
        $produk = $this->actingAs($admin)->createProduk();
        $gaji = $this->createGaji($karyawan, $admin->id);

        $this->actingAs($admin)->get('/absensi/rekap')->assertOk();
        $this->actingAs($admin)->get("/layanan/{$layanan->id}")->assertOk();
        $this->actingAs($admin)->get("/layanan/{$layanan->id}/edit")->assertOk();
        $this->actingAs($admin)->get("/produk/{$produk->id}")->assertOk();
        $this->actingAs($admin)->get("/gaji/{$gaji->id}")->assertOk();
        $this->actingAs($admin)->get("/gaji/{$gaji->id}/slip")->assertOk();
    }

    public function test_transaksi_destroy_route_is_registered_but_forbidden_for_non_super_admin(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $pelanggan = Pelanggan::create([
            'nama' => 'Pelanggan Route',
            'status' => 'aktif',
        ]);

        $transaksi = Transaksi::create([
            'nomor_transaksi' => 'TRX-TEST-001',
            'tanggal_transaksi' => now(),
            'pelanggan_id' => $pelanggan->id,
            'kasir_id' => $kasir->id,
            'subtotal' => 10000,
            'diskon' => 0,
            'total' => 10000,
            'bayar' => 10000,
            'kembalian' => 0,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'status' => 'selesai',
        ]);

        $this->actingAs($kasir)
            ->delete("/transaksi/{$transaksi->id}")
            ->assertStatus(403);
    }

    private function createKaryawan(array $overrides = []): Karyawan
    {
        return Karyawan::create(array_merge([
            'nama' => 'Karyawan Route',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1200000,
        ], $overrides));
    }

    private function createLayanan(array $overrides = []): Layanan
    {
        return Layanan::create(array_merge([
            'nama' => 'Layanan Route',
            'harga_fleksibel' => false,
            'harga_default' => 70000,
            'insentif_karyawan' => 10000,
            'status' => 'aktif',
        ], $overrides));
    }

    private function createProduk(array $overrides = []): Produk
    {
        return Produk::create(array_merge([
            'nama' => 'Produk Route',
            'satuan' => 'pcs',
            'stok' => 10,
            'stok_minimal' => 1,
            'harga_jual' => 30000,
            'harga_beli' => 20000,
            'harga_fleksibel' => false,
            'status' => 'aktif',
        ], $overrides));
    }

    private function createGaji(Karyawan $karyawan, int $userId): Gaji
    {
        return Gaji::create([
            'karyawan_id' => $karyawan->id,
            'periode' => now()->format('Y-m'),
            'tanggal_mulai' => now()->startOfMonth()->toDateString(),
            'tanggal_selesai' => now()->endOfMonth()->toDateString(),
            'gaji_pokok' => 1200000,
            'total_insentif' => 0,
            'bonus' => 0,
            'thr' => 0,
            'potongan' => 0,
            'total_gaji' => 1200000,
            'jumlah_hadir' => 0,
            'jumlah_izin' => 0,
            'jumlah_sakit' => 0,
            'jumlah_alpha' => 0,
            'status' => 'draft',
            'diajukan_oleh' => $userId,
        ]);
    }
}
