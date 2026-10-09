<?php

namespace Tests\Feature\Absensi;

use App\Models\Absensi;
use App\Models\AbsensiBarcode;
use App\Models\Karyawan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class AbsensiScanTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_scan_checkin_saves_attendance_photo_location_and_marks_barcode_used(): void
    {
        Storage::fake('public');

        $user = $this->createUserWithRole('karyawan');
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Absensi',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1000000,
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id,
        ]);

        $barcode = AbsensiBarcode::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => today(),
            'tipe' => 'checkin',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson(route('absensi.process-scan'), [
            'barcode' => $barcode->barcode,
            'foto' => $this->base64Image(),
            'latitude' => -5.147665,
            'longitude' => 119.432732,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'tipe' => 'checkin',
            ]);

        $absensi = Absensi::firstOrFail();

        $this->assertSame($karyawan->id, $absensi->karyawan_id);
        $this->assertNotNull($absensi->jam_masuk);
        $this->assertNull($absensi->jam_keluar);
        $this->assertSame('hadir', $absensi->status);
        $this->assertSame('pending', $absensi->status_verifikasi);
        $this->assertSame($barcode->barcode, $absensi->barcode);
        $this->assertNotNull($absensi->foto_masuk);
        $this->assertTrue(Storage::disk('public')->exists($absensi->foto_masuk));

        $this->assertDatabaseHas('absensi_barcodes', [
            'id' => $barcode->id,
            'is_used' => true,
        ]);
    }

    public function test_scan_checkout_updates_existing_attendance_and_checkout_photo(): void
    {
        Storage::fake('public');

        $user = $this->createUserWithRole('karyawan');
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Checkout',
            'jabatan' => 'Stylist',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1000000,
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id,
        ]);

        $absensi = Absensi::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => today(),
            'jam_masuk' => '08:00',
            'status' => 'hadir',
            'foto_masuk' => 'absensi/photos/checkin/' . today()->format('Y-m-d') . '/existing.jpg',
            'status_verifikasi' => 'pending',
        ]);

        $barcode = AbsensiBarcode::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => today(),
            'tipe' => 'checkout',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson(route('absensi.process-scan'), [
            'barcode' => $barcode->barcode,
            'foto' => $this->base64Image(),
            'latitude' => -5.147665,
            'longitude' => 119.432732,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'tipe' => 'checkout',
            ]);

        $absensi->refresh();

        $this->assertNotNull($absensi->jam_keluar);
        $this->assertNotNull($absensi->foto_keluar);
        $this->assertTrue(Storage::disk('public')->exists($absensi->foto_keluar));
        $this->assertDatabaseHas('absensi_barcodes', [
            'id' => $barcode->id,
            'is_used' => true,
        ]);
    }

    public function test_legacy_checkin_is_rejected_for_karyawan_role(): void
    {
        $user = $this->createUserWithRole('karyawan');

        $this->actingAs($user)
            ->post(route('absensi.checkin'))
            ->assertRedirect()
            ->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function test_scan_without_gps_is_accepted_and_marked_pending_location(): void
    {
        Storage::fake('public');

        $user = $this->createUserWithRole('karyawan');
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Tanpa GPS',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1000000,
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id,
        ]);

        $barcode = AbsensiBarcode::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => today(),
            'tipe' => 'checkin',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->postJson(route('absensi.process-scan'), [
            'barcode' => $barcode->barcode,
            'foto' => $this->base64Image(),
        ])->assertOk()
            ->assertJson(['success' => true]);

        $absensi = Absensi::firstOrFail();
        $this->assertTrue($absensi->lokasi_missing);
        $this->assertSame('pending', $absensi->status_verifikasi);
        $this->assertStringContainsString('Lokasi GPS tidak tersedia', $absensi->catatan_verifikasi);
    }

    public function test_barcode_cannot_be_submitted_twice(): void
    {
        Storage::fake('public');

        $user = $this->createUserWithRole('karyawan');
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Double Submit',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1000000,
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id,
        ]);

        $barcode = AbsensiBarcode::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => today(),
            'tipe' => 'checkin',
            'created_by' => $user->id,
        ]);

        $payload = [
            'barcode' => $barcode->barcode,
            'foto' => $this->base64Image(),
            'latitude' => -5.147665,
            'longitude' => 119.432732,
        ];

        $this->actingAs($user)->postJson(route('absensi.process-scan'), $payload)->assertOk();
        $this->actingAs($user)->postJson(route('absensi.process-scan'), $payload)
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        $this->assertDatabaseCount('absensis', 1);
    }

    public function test_cleanup_expired_barcodes_requires_post_request(): void
    {
        $user = $this->createUserWithRole('admin');
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Barcode Kedaluwarsa',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1000000,
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id,
        ]);

        $barcode = AbsensiBarcode::create([
            'karyawan_id' => $karyawan->id,
            'tanggal' => today()->subDay(),
            'tipe' => 'checkin',
            'created_by' => $user->id,
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($user)
            ->get(route('absensi.barcodes.cleanup'))
            ->assertNotFound();

        $this->actingAs($user)
            ->post(route('absensi.barcodes.cleanup'))
            ->assertRedirect(route('absensi.barcodes.index'));

        $this->assertDatabaseMissing('absensi_barcodes', ['id' => $barcode->id]);
    }

    private function base64Image(): string
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wn7sEwAAAAASUVORK5CYII=';
    }
}
