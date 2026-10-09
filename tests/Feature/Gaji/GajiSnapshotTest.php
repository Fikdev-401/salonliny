<?php

namespace Tests\Feature\Gaji;

use App\Models\Gaji;
use App\Models\Karyawan;
use App\Models\Pelanggan;
use App\Models\RiwayatGajiKaryawan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class GajiSnapshotTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_gaji_uses_salary_history_snapshot_and_paid_status_creates_pengeluaran(): void
    {
        $admin = $this->createUserWithRole('admin');
        $pimpinan = $this->createUserWithRole('pimpinan');
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Gaji',
            'jabatan' => 'Stylist',
            'tanggal_bergabung' => '2026-01-01',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1200000,
        ]);

        RiwayatGajiKaryawan::create([
            'karyawan_id' => $karyawan->id,
            'berlaku_mulai' => '2026-01-01',
            'gaji_pokok' => 1200000,
            'created_by' => $admin->id,
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => 'Pelanggan Gaji',
            'status' => 'aktif',
        ]);

        $transaksi = Transaksi::create([
            'nomor_transaksi' => 'TRX-GAJI-001',
            'tanggal_transaksi' => '2026-01-15 10:00:00',
            'pelanggan_id' => $pelanggan->id,
            'kasir_id' => $admin->id,
            'subtotal' => 100000,
            'diskon' => 0,
            'total' => 100000,
            'bayar' => 100000,
            'kembalian' => 0,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'status' => 'selesai',
            'cabang_id' => $admin->cabang_id,
        ]);

        TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'tipe' => 'layanan',
            'nama_item' => 'Haircut',
            'jumlah' => 1,
            'satuan' => 'x',
            'harga_satuan' => 100000,
            'total' => 100000,
            'karyawan_utama_id' => $karyawan->id,
            'insentif_utama' => 25000,
        ]);

        $this->actingAs($admin)->post('/gaji', [
            'karyawan_id' => $karyawan->id,
            'periode' => '2026-01',
            'bonus' => 10000,
            'thr' => 0,
            'potongan' => 5000,
        ])->assertRedirect('/gaji');

        $gaji = Gaji::firstOrFail();
        $this->assertEquals(1200000.0, (float) $gaji->gaji_pokok);
        $this->assertEquals(25000.0, (float) $gaji->total_insentif);

        RiwayatGajiKaryawan::create([
            'karyawan_id' => $karyawan->id,
            'berlaku_mulai' => '2026-02-01',
            'gaji_pokok' => 1500000,
            'created_by' => $admin->id,
        ]);
        $karyawan->update(['gaji_pokok' => 1500000]);

        $gaji->refresh();
        $this->assertEquals(1200000.0, (float) $gaji->gaji_pokok);

        $this->actingAs($pimpinan)->post("/gaji/{$gaji->id}/approve")->assertRedirect('/gaji');
        $this->actingAs($admin)->post("/gaji/{$gaji->id}/bayar", [
            'tanggal_bayar' => '2026-01-31',
        ])->assertRedirect('/gaji');

        $this->assertDatabaseHas('pengeluarans', [
            'sumber_tipe' => Gaji::class,
            'sumber_id' => $gaji->id,
            'is_system_generated' => true,
            'jumlah' => (float) $gaji->refresh()->total_gaji,
        ]);
    }
}
