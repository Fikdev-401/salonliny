<?php

namespace Tests\Feature\Transaksi;

use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class TransaksiPresentationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_transaksi_index_shows_aggregated_insentif_column(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        [$transaksi] = $this->seedTransaksiWithLayanan($superAdmin, '0812-3456-7890');

        $this->actingAs($superAdmin)
            ->get('/transaksi')
            ->assertOk()
            ->assertSee('Insentif')
            ->assertSee($transaksi->nomor_transaksi)
            ->assertSee('Rp 20.000', false);
    }

    public function test_transaksi_show_renders_whatsapp_share_button_when_customer_has_phone(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        [$transaksi, $pelanggan] = $this->seedTransaksiWithLayanan($superAdmin, '0812 3456 7890');

        $this->actingAs($superAdmin)
            ->get("/transaksi/{$transaksi->id}")
            ->assertOk()
            ->assertSee('Share WA')
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee($pelanggan->nama);
    }

    public function test_transaksi_show_disables_whatsapp_share_when_customer_phone_is_missing(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        [$transaksi] = $this->seedTransaksiWithLayanan($superAdmin, null);

        $this->actingAs($superAdmin)
            ->get("/transaksi/{$transaksi->id}")
            ->assertOk()
            ->assertSee('Share WA')
            ->assertDontSee('https://wa.me/', false);
    }

    public function test_transaksi_edit_form_is_hydrated_from_existing_transaction_data(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        [$transaksi, $pelanggan, $layanan, $karyawan] = $this->seedTransaksiWithLayanan($superAdmin, '081234567890');

        $this->actingAs($superAdmin)
            ->get("/transaksi/{$transaksi->id}/edit")
            ->assertOk()
            ->assertSee($transaksi->nomor_transaksi)
            ->assertSee($pelanggan->nama)
            ->assertSee($layanan->nama)
            ->assertSee($karyawan->nama)
            ->assertSee('15000', false)
            ->assertSee('5000', false);
    }

    private function seedTransaksiWithLayanan($user, ?string $phone): array
    {
        $pelanggan = Pelanggan::create([
            'nama' => 'Andi Pratama',
            'no_hp' => $phone,
            'status' => 'aktif',
        ]);
        $karyawan = Karyawan::create([
            'nama' => 'Siti Aminah',
            'jabatan' => 'Stylist',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1500000,
        ]);
        $pendukung = Karyawan::create([
            'nama' => 'Budi Wijaya',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1400000,
        ]);
        $layanan = Layanan::create([
            'nama' => 'Creambath',
            'harga_default' => 75000,
            'harga_fleksibel' => false,
            'status' => 'aktif',
        ]);

        $transaksi = Transaksi::create([
            'nomor_transaksi' => 'TRX-PRESENTASI-001',
            'tanggal_transaksi' => '2026-04-03 16:54:00',
            'pelanggan_id' => $pelanggan->id,
            'kasir_id' => $user->id,
            'cabang_id' => $user->cabang_id,
            'subtotal' => 75000,
            'diskon' => 0,
            'total' => 75000,
            'bayar' => 100000,
            'kembalian' => 25000,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'status' => 'selesai',
        ]);

        TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'tipe' => 'layanan',
            'layanan_id' => $layanan->id,
            'nama_item' => $layanan->nama,
            'jumlah' => 1,
            'satuan' => 'x',
            'harga_satuan' => 75000,
            'total' => 75000,
            'karyawan_utama_id' => $karyawan->id,
            'insentif_utama' => 15000,
            'karyawan_pendukung_1_id' => $pendukung->id,
            'insentif_pendukung_1' => 5000,
        ]);

        return [$transaksi, $pelanggan, $layanan, $karyawan];
    }
}
