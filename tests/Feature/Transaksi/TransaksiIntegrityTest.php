<?php

namespace Tests\Feature\Transaksi;

use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class TransaksiIntegrityTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_transaksi_store_validates_item_based_on_type(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $pelanggan = $this->createPelanggan();
        $karyawan = $this->actingAs($kasir)->createKaryawan();
        $produk = $this->actingAs($kasir)->createProduk([
            'nama' => 'Pomade',
            'stok' => 5,
            'harga_jual' => 50000,
            'harga_beli' => 30000,
            'harga_fleksibel' => false,
        ]);

        $response = $this->actingAs($kasir)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $produk->id,
                    'jumlah' => 1,
                    'harga_satuan' => 1,
                    'karyawan_utama_id' => $karyawan->id,
                ],
            ],
            'diskon' => 0,
            'bayar' => 10000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertSessionHasErrors(['items.0.item_id']);
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_transaksi_store_rejects_when_product_stock_is_insufficient(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $pelanggan = $this->createPelanggan();
        $produk = $this->actingAs($kasir)->createProduk([
            'nama' => 'Hair Tonic',
            'stok' => 1,
            'harga_jual' => 45000,
            'harga_beli' => 25000,
            'harga_fleksibel' => false,
        ]);

        $response = $this->actingAs($kasir)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'produk',
                    'item_id' => $produk->id,
                    'jumlah' => 3,
                    'harga_satuan' => 1000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 100000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertSessionHasErrors(['items.0.jumlah']);
        $this->assertDatabaseCount('transaksis', 0);
        $this->assertDatabaseHas('produks', [
            'id' => $produk->id,
            'stok' => 1,
        ]);
    }

    public function test_transaksi_store_rejects_discount_larger_than_subtotal(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $pelanggan = $this->createPelanggan();
        $karyawan = $this->actingAs($kasir)->createKaryawan();
        $layanan = $this->createLayanan([
            'nama' => 'Haircut',
            'harga_default' => 100000,
            'harga_fleksibel' => false,
        ]);

        $response = $this->actingAs($kasir)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 1000,
                    'karyawan_utama_id' => $karyawan->id,
                ],
            ],
            'diskon' => 150000,
            'bayar' => 1000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertSessionHasErrors(['diskon']);
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_transaksi_store_rejects_layanan_without_primary_worker(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $pelanggan = $this->createPelanggan();
        $layanan = $this->createLayanan([
            'nama' => 'Creambath',
            'harga_default' => 75000,
            'harga_fleksibel' => false,
        ]);

        $response = $this->actingAs($kasir)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 75000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 75000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertSessionHasErrors(['items.0.karyawan_utama_id']);
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_transaksi_store_rejects_when_payment_is_less_than_total(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $pelanggan = $this->createPelanggan();
        $karyawan = $this->actingAs($kasir)->createKaryawan();
        $layanan = $this->createLayanan([
            'nama' => 'Hair Spa',
            'harga_default' => 90000,
            'harga_fleksibel' => false,
        ]);

        $response = $this->actingAs($kasir)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 90000,
                    'karyawan_utama_id' => $karyawan->id,
                ],
            ],
            'diskon' => 0,
            'bayar' => 50000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertSessionHasErrors(['bayar']);
        $this->assertDatabaseCount('transaksis', 0);
    }

    public function test_transaksi_store_uses_server_side_prices_and_updates_stock_safely(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $pelanggan = $this->createPelanggan();
        $karyawan = $this->actingAs($kasir)->createKaryawan();
        $layanan = $this->createLayanan([
            'nama' => 'Coloring',
            'harga_default' => 120000,
            'harga_fleksibel' => false,
        ]);
        $produk = $this->actingAs($kasir)->createProduk([
            'nama' => 'Shampoo',
            'stok' => 5,
            'harga_jual' => 50000,
            'harga_beli' => 35000,
            'harga_fleksibel' => false,
        ]);

        $response = $this->actingAs($kasir)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 1,
                    'karyawan_utama_id' => $karyawan->id,
                ],
                [
                    'tipe' => 'produk',
                    'item_id' => $produk->id,
                    'jumlah' => 2,
                    'harga_satuan' => 1,
                ],
            ],
            'diskon' => 20000,
            'bayar' => 200000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertRedirect();

        $transaksi = Transaksi::query()->firstOrFail();
        $this->assertEquals(220000.0, (float) $transaksi->subtotal);
        $this->assertEquals(20000.0, (float) $transaksi->diskon);
        $this->assertEquals(200000.0, (float) $transaksi->total);
        $this->assertEquals('lunas', $transaksi->status_pembayaran);
        $this->assertEquals('selesai', $transaksi->status);

        $details = TransaksiDetail::where('transaksi_id', $transaksi->id)->orderBy('id')->get();
        $this->assertCount(2, $details);
        $this->assertEquals(120000.0, (float) $details[0]->harga_satuan);
        $this->assertEquals(50000.0, (float) $details[1]->harga_satuan);

        $this->assertDatabaseHas('produks', [
            'id' => $produk->id,
            'stok' => 3,
        ]);
    }

    public function test_non_super_admin_cannot_transact_product_from_other_cabang(): void
    {
        $kasir = $this->createUserWithRole('kasir');
        $otherCabang = $this->createCabang([
            'nama' => 'Cabang Lain',
            'is_default' => false,
        ]);
        $pelanggan = $this->createPelanggan();
        $produk = $this->actingAs($kasir)->createProduk([
            'nama' => 'Produk Cabang Lain',
            'cabang_id' => $otherCabang->id,
            'stok' => 5,
            'harga_jual' => 50000,
        ]);

        $response = $this->actingAs($kasir)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => now()->toDateTimeString(),
            'items' => [
                [
                    'tipe' => 'produk',
                    'item_id' => $produk->id,
                    'jumlah' => 1,
                    'harga_satuan' => 50000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 50000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertSessionHasErrors(['items.0.item_id']);
        $this->assertDatabaseCount('transaksis', 0);
    }

    private function createPelanggan(): Pelanggan
    {
        return Pelanggan::create([
            'nama' => 'Pelanggan Test',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);
    }

    private function createLayanan(array $overrides = []): Layanan
    {
        return Layanan::create(array_merge([
            'nama' => 'Layanan Test',
            'deskripsi' => null,
            'harga_fleksibel' => false,
            'harga_default' => 50000,
            'durasi_menit' => 30,
            'kategori' => 'Perawatan',
            'status' => 'aktif',
        ], $overrides));
    }

    private function createProduk(array $overrides = []): Produk
    {
        return Produk::create(array_merge([
            'nama' => 'Produk Test',
            'kode' => null,
            'deskripsi' => null,
            'kategori' => 'Perawatan',
            'satuan' => 'pcs',
            'stok' => 10,
            'stok_minimal' => 1,
            'harga_jual' => 25000,
            'harga_beli' => 10000,
            'harga_fleksibel' => false,
            'status' => 'aktif',
        ], $overrides));
    }

    private function createKaryawan(array $overrides = []): Karyawan
    {
        return Karyawan::create(array_merge([
            'nama' => 'Karyawan Test',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1000000,
        ], $overrides));
    }
}
