<?php

namespace Tests\Feature\Gaji;

use App\Models\Absensi;
use App\Models\Gaji;
use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\RiwayatGajiKaryawan;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class GajiPreviewAndSyncTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_gaji_preview_returns_salary_snapshot_and_attendance_summary(): void
    {
        $admin = $this->createUserWithRole('admin');
        $karyawan = $this->createKaryawan();

        RiwayatGajiKaryawan::create([
            'karyawan_id' => $karyawan->id,
            'berlaku_mulai' => '2026-04-01',
            'gaji_pokok' => 2000000,
            'created_by' => $admin->id,
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => 'Pelanggan Preview',
            'status' => 'aktif',
        ]);

        $transaksi = Transaksi::create([
            'nomor_transaksi' => 'TRX-GAJI-PREVIEW',
            'tanggal_transaksi' => '2026-04-10 10:00:00',
            'pelanggan_id' => $pelanggan->id,
            'kasir_id' => $admin->id,
            'cabang_id' => $admin->cabang_id,
            'subtotal' => 120000,
            'diskon' => 0,
            'total' => 120000,
            'bayar' => 120000,
            'kembalian' => 0,
            'metode_pembayaran' => 'tunai',
            'status_pembayaran' => 'lunas',
            'status' => 'selesai',
        ]);

        TransaksiDetail::create([
            'transaksi_id' => $transaksi->id,
            'tipe' => 'layanan',
            'nama_item' => 'Coloring',
            'jumlah' => 1,
            'satuan' => 'x',
            'harga_satuan' => 120000,
            'total' => 120000,
            'karyawan_utama_id' => $karyawan->id,
            'insentif_utama' => 30000,
        ]);

        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-04-02',
            'status' => 'hadir',
        ]);
        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-04-03',
            'status' => 'izin',
        ]);
        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-04-04',
            'status' => 'sakit',
        ]);
        Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => '2026-04-05',
            'status' => 'alpha',
        ]);

        $this->actingAs($admin)
            ->getJson('/gaji/preview?karyawan_id=' . $karyawan->id . '&periode=2026-04')
            ->assertOk()
            ->assertJson([
                'gaji_pokok' => 2000000.0,
                'total_insentif' => 30000.0,
                'jumlah_hadir' => 1,
                'jumlah_izin' => 1,
                'jumlah_sakit' => 1,
                'jumlah_alpha' => 1,
                'base_total' => 2030000.0,
            ]);
    }

    public function test_draft_gaji_resyncs_on_transaction_create_update_delete_but_not_after_approved(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        $pelanggan = Pelanggan::create([
            'nama' => 'Pelanggan Sinkron',
            'status' => 'aktif',
        ]);
        $karyawan = $this->createKaryawan();
        $layanan = Layanan::create([
            'nama' => 'Hair Spa',
            'harga_default' => 100000,
            'harga_fleksibel' => false,
            'status' => 'aktif',
        ]);

        RiwayatGajiKaryawan::create([
            'karyawan_id' => $karyawan->id,
            'berlaku_mulai' => '2026-04-01',
            'gaji_pokok' => 2500000,
            'created_by' => $superAdmin->id,
        ]);

        $this->actingAs($superAdmin)->post('/gaji', [
            'karyawan_id' => $karyawan->id,
            'periode' => '2026-04',
            'bonus' => 100000,
            'thr' => 0,
            'potongan' => 50000,
        ])->assertRedirect('/gaji');

        $gaji = Gaji::firstOrFail();
        $this->assertEquals(0.0, (float) $gaji->total_insentif);

        $this->actingAs($superAdmin)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => '2026-04-10 09:00:00',
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 100000,
                    'karyawan_utama_id' => $karyawan->id,
                    'insentif_utama' => 25000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 100000,
            'metode_pembayaran' => 'tunai',
        ])->assertRedirect();

        $transaksi = Transaksi::firstOrFail();
        $gaji->refresh();
        $this->assertEquals(25000.0, (float) $gaji->total_insentif);
        $this->assertEquals(2575000.0, (float) $gaji->total_gaji);

        $this->actingAs($superAdmin)->put("/transaksi/{$transaksi->id}", [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => '2026-04-10 09:00:00',
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 100000,
                    'karyawan_utama_id' => $karyawan->id,
                    'insentif_utama' => 40000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 100000,
            'metode_pembayaran' => 'tunai',
        ])->assertRedirect("/transaksi/{$transaksi->id}");

        $gaji->refresh();
        $this->assertEquals(40000.0, (float) $gaji->total_insentif);

        $this->actingAs($superAdmin)->delete("/transaksi/{$transaksi->id}")
            ->assertRedirect('/transaksi');

        $gaji->refresh();
        $this->assertEquals(0.0, (float) $gaji->total_insentif);

        $this->actingAs($superAdmin)->post("/gaji/{$gaji->id}/approve")->assertRedirect('/gaji');

        $this->actingAs($superAdmin)->post('/transaksi', [
            'pelanggan_id' => $pelanggan->id,
            'tanggal_transaksi' => '2026-04-11 09:00:00',
            'items' => [
                [
                    'tipe' => 'layanan',
                    'item_id' => $layanan->id,
                    'jumlah' => 1,
                    'harga_satuan' => 100000,
                    'karyawan_utama_id' => $karyawan->id,
                    'insentif_utama' => 30000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 100000,
            'metode_pembayaran' => 'tunai',
        ])->assertRedirect();

        $gaji->refresh();
        $this->assertEquals('disetujui', $gaji->status);
        $this->assertEquals(0.0, (float) $gaji->total_insentif);
    }

    private function createKaryawan(array $overrides = []): Karyawan
    {
        return Karyawan::create(array_merge([
            'nama' => 'Ahmad Santoso',
            'jabatan' => 'Tukang Cukur Senior',
            'tanggal_bergabung' => '2026-01-01',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 2500000,
        ], $overrides));
    }
}
