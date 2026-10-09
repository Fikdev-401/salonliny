<?php

namespace Tests\Feature\Finance;

use App\Models\Inventaris;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class PembelianWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_product_purchase_updates_stock_and_creates_linked_pengeluaran(): void
    {
        $admin = $this->createUserWithRole('admin');
        $produk = $this->actingAs($admin)->createProduk([
            'nama' => 'Pomade Jual',
            'stok' => 3,
        ]);

        $this->actingAs($admin)->post('/pembelian', [
            'tanggal' => now()->toDateString(),
            'jenis_pembelian' => 'produk_jual',
            'produk_id' => $produk->id,
            'jumlah' => 4,
            'harga_satuan' => 20000,
            'supplier' => 'Supplier Test',
        ])->assertRedirect('/pembelian');

        $pembelian = Pembelian::firstOrFail();

        $this->assertDatabaseHas('produks', [
            'id' => $produk->id,
            'stok' => 7,
        ]);
        $this->assertDatabaseHas('pengeluarans', [
            'sumber_tipe' => Pembelian::class,
            'sumber_id' => $pembelian->id,
            'is_system_generated' => true,
            'jumlah' => 80000,
        ]);
    }

    public function test_inventory_purchase_creates_inventory_record_and_linked_pengeluaran(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->post('/pembelian', [
            'tanggal' => now()->toDateString(),
            'jenis_pembelian' => 'alat_inventaris',
            'jumlah' => 2,
            'harga_satuan' => 150000,
            'supplier' => 'Supplier Inventaris',
            'nama_aset' => 'Kursi Barber',
            'jenis_aset' => 'Furniture',
            'satuan_aset' => 'unit',
            'status_aset' => 'bagus',
        ])->assertRedirect('/pembelian');

        $inventaris = Inventaris::firstOrFail();
        $pembelian = Pembelian::firstOrFail();

        $this->assertNotNull($pembelian->inventaris_id);
        $this->assertNull($pembelian->produk_id);
        $this->assertSame('Kursi Barber', $inventaris->nama_aset);
        $this->assertDatabaseHas('pengeluarans', [
            'sumber_tipe' => Pembelian::class,
            'sumber_id' => $pembelian->id,
            'jumlah' => 300000,
        ]);
    }

    public function test_non_super_admin_cannot_purchase_product_from_other_cabang(): void
    {
        $admin = $this->createUserWithRole('admin');
        $otherCabang = $this->createCabang([
            'nama' => 'Cabang Lain',
            'is_default' => false,
        ]);
        $produk = $this->actingAs($admin)->createProduk([
            'nama' => 'Produk Beda Cabang',
            'cabang_id' => $otherCabang->id,
            'stok' => 3,
        ]);

        $this->actingAs($admin)->post('/pembelian', [
            'tanggal' => now()->toDateString(),
            'jenis_pembelian' => 'produk_jual',
            'produk_id' => $produk->id,
            'jumlah' => 4,
            'harga_satuan' => 20000,
            'supplier' => 'Supplier Test',
        ])->assertSessionHasErrors(['produk_id']);

        $this->assertDatabaseCount('pembelians', 0);
    }

    private function createProduk(array $overrides = []): Produk
    {
        return Produk::create(array_merge([
            'nama' => 'Produk Finance',
            'satuan' => 'pcs',
            'stok' => 5,
            'stok_minimal' => 1,
            'harga_jual' => 40000,
            'harga_beli' => 15000,
            'harga_fleksibel' => false,
            'status' => 'aktif',
        ], $overrides));
    }
}
