<?php

namespace Tests\Feature\Gaji;

use App\Models\Gaji;
use App\Models\Karyawan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class GajiStateMachineTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_only_pimpinan_or_super_admin_can_approve_draft_gaji(): void
    {
        $admin = $this->createUserWithRole('admin');
        $pimpinan = $this->createUserWithRole('pimpinan');
        $gaji = $this->createGaji(['diajukan_oleh' => $admin->id]);

        $this->actingAs($admin)
            ->post("/gaji/{$gaji->id}/approve")
            ->assertForbidden();

        $this->actingAs($pimpinan)
            ->post("/gaji/{$gaji->id}/approve")
            ->assertRedirect('/gaji');

        $this->assertSame('disetujui', $gaji->fresh()->status);
        $this->assertSame($pimpinan->id, $gaji->fresh()->disetujui_oleh);
    }

    public function test_gaji_can_only_be_paid_after_approval(): void
    {
        $admin = $this->createUserWithRole('admin');
        $pimpinan = $this->createUserWithRole('pimpinan');
        $gaji = $this->createGaji(['diajukan_oleh' => $admin->id]);

        $this->actingAs($admin)
            ->post("/gaji/{$gaji->id}/bayar", ['tanggal_bayar' => now()->toDateString()])
            ->assertStatus(422);

        $this->actingAs($pimpinan)->post("/gaji/{$gaji->id}/approve");

        $this->actingAs($admin)
            ->post("/gaji/{$gaji->id}/bayar", ['tanggal_bayar' => now()->toDateString()])
            ->assertRedirect('/gaji');

        $this->assertSame('dibayar', $gaji->fresh()->status);
    }

    public function test_paid_gaji_cannot_be_edited_by_admin(): void
    {
        $admin = $this->createUserWithRole('admin');
        $superAdmin = $this->createUserWithRole('super-admin');
        $gaji = $this->createGaji([
            'status' => 'dibayar',
            'diajukan_oleh' => $admin->id,
            'disetujui_oleh' => $superAdmin->id,
            'tanggal_bayar' => now()->toDateString(),
        ]);

        $this->actingAs($admin)
            ->put("/gaji/{$gaji->id}", [
                'bonus' => 10000,
                'thr' => 0,
                'potongan' => 0,
                'keterangan' => 'Koreksi pembayaran',
            ])
            ->assertStatus(422);
    }

    private function createGaji(array $overrides = []): Gaji
    {
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Gaji',
            'jabatan' => 'Barber',
            'tanggal_bergabung' => '2026-01-01',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 2000000,
        ]);

        return Gaji::create(array_merge([
            'karyawan_id' => $karyawan->id,
            'periode' => '2026-06',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-06-30',
            'gaji_pokok' => 2000000,
            'total_insentif' => 0,
            'bonus' => 0,
            'thr' => 0,
            'potongan' => 0,
            'total_gaji' => 2000000,
            'jumlah_hadir' => 0,
            'jumlah_izin' => 0,
            'jumlah_sakit' => 0,
            'jumlah_alpha' => 0,
            'status' => 'draft',
        ], $overrides));
    }
}
