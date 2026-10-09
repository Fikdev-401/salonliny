<?php

namespace Tests\Feature\Laporan;

use App\Models\Karyawan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class LaporanRoutesTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_laporan_html_routes_are_accessible_for_admin(): void
    {
        $admin = $this->createUserWithRole('admin');

        $routes = [
            '/laporan',
            '/laporan/pendapatan',
            '/laporan/pengeluaran',
            '/laporan/transaksi',
            '/laporan/absensi',
            '/laporan/gaji',
            '/laporan/layanan',
            '/laporan/performa-karyawan',
            '/laporan/laba-rugi',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get($route)
                ->assertOk();
        }
    }

    public function test_performa_karyawan_detail_route_returns_json_without_500(): void
    {
        $admin = $this->createUserWithRole('admin');
        $karyawan = $this->actingAs($admin)->createKaryawan();

        $response = $this->actingAs($admin)
            ->get("/laporan/performa-karyawan/{$karyawan->id}/detail");

        $response->assertOk();
        $response->assertJsonStructure([
            'karyawan',
            'bulan',
            'details',
            'total_insentif',
        ]);
    }

    public function test_laba_rugi_pdf_route_is_accessible(): void
    {
        $admin = $this->createUserWithRole('admin');

        $response = $this->actingAs($admin)
            ->get('/laporan/laba-rugi?format=pdf');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    private function createKaryawan(array $overrides = []): Karyawan
    {
        return Karyawan::create(array_merge([
            'nama' => 'Karyawan Laporan',
            'jabatan' => 'Stylist',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1500000,
        ], $overrides));
    }
}
