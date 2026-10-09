<?php

namespace Tests\Feature\Transaksi;

use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class TransaksiMaintenanceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_super_admin_can_update_transaksi_and_reconcile_product_stock(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        $pelanggan = $this->createPelanggan();
        $karyawan = $this->createKaryawan();
        $layanan = $this->createLayanan();
        $pomade = $this->actingAs($superAdmin)->createProduk([
            'nama' => 'Pomade',
            'stok' => 10,
            'harga_jual' => 50000,
        ]);
        $wax = $this->actingAs($superAdmin)->createProduk([
            'nama' => 'Wax',
            'stok' => 8,
            'harga_jual' => 40000,
        ]);

        $this->actingAs($superAdmin)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 1000,
                    'karyawan_utama_id' => $karyawan->id,
                    'insentif_utama' => 15000,
                ],
                [
                    'tipe' => 'produk',
                    'item_id' => $pomade->id,
                    'jumlah' => 2,
                    'harga_satuan' => 1000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 200000,
            'metode_pembayaran' => 'tunai',
        ])->assertRedirect();

        $transaksi = Transaksi::firstOrFail();

        $this->actingAs($superAdmin)->put("/transaksi/{$transaksi->id}", [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->addHour()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 2,
                    'harga_satuan' => 1000,
                    'karyawan_utama_id' => $karyawan->id,
                    'insentif_utama' => 30000,
                ],
                [
                    'tipe' => 'produk',
                    'item_id' => $wax->id,
                    'jumlah' => 1,
                    'harga_satuan' => 1000,
                ],
            ],
            'diskon' => 10000,
            'bayar' => 250000,
            'metode_pembayaran' => 'qris',
        ])->assertRedirect("/transaksi/{$transaksi->id}");

        $transaksi->refresh();

        $this->assertEquals(240000.0, (float) $transaksi->subtotal);
        $this->assertEquals(230000.0, (float) $transaksi->total);
        $this->assertEquals('qris', $transaksi->metode_pembayaran);
        $this->assertDatabaseHas('produks', [
            'id' => $pomade->id,
            'stok' => 10,
        ]);
        $this->assertDatabaseHas('produks', [
            'id' => $wax->id,
            'stok' => 7,
        ]);
    }

    public function test_super_admin_can_delete_transaksi_and_restore_stock_and_customer_stats(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        $pelanggan = $this->createPelanggan();
        $produk = $this->actingAs($superAdmin)->createProduk([
            'stok' => 5,
            'harga_jual' => 30000,
        ]);

        $this->actingAs($superAdmin)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'produk',
                    'item_id' => $produk->id,
                    'jumlah' => 2,
                    'harga_satuan' => 999,
                ],
            ],
            'diskon' => 0,
            'bayar' => 60000,
            'metode_pembayaran' => 'tunai',
        ])->assertRedirect();

        $transaksi = Transaksi::firstOrFail();

        $this->actingAs($superAdmin)
            ->delete("/transaksi/{$transaksi->id}")
            ->assertRedirect('/transaksi');

        $this->assertSoftDeleted('transaksis', ['id' => $transaksi->id]);
        $this->assertDatabaseHas('produks', [
            'id' => $produk->id,
            'stok' => 5,
        ]);
        $this->assertDatabaseHas('pelanggans', [
            'id' => $pelanggan->id,
            'total_kunjungan' => 0,
            'terakhir_kunjungan' => null,
        ]);
    }

    private function createPelanggan(): Pelanggan
    {
        return Pelanggan::create([
            'nama' => 'Pelanggan Maintenance',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);
    }

    private function createKaryawan(array $overrides = []): Karyawan
    {
        return Karyawan::create(array_merge([
            'nama' => 'Karyawan Maintenance',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1000000,
        ], $overrides));
    }

    private function createLayanan(array $overrides = []): Layanan
    {
        return Layanan::create(array_merge([
            'nama' => 'Haircut Premium',
            'harga_default' => 100000,
            'harga_fleksibel' => false,
            'status' => 'aktif',
        ], $overrides));
    }

    private function createProduk(array $overrides = []): Produk
    {
        return Produk::create(array_merge([
            'nama' => 'Produk Maintenance',
            'satuan' => 'pcs',
            'stok' => 10,
            'stok_minimal' => 1,
            'harga_jual' => 25000,
            'harga_beli' => 10000,
            'harga_fleksibel' => false,
            'status' => 'aktif',
        ], $overrides));
    }
}
