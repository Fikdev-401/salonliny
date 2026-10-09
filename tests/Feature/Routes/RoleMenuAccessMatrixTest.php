<?php

namespace Tests\Feature\Routes;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class RoleMenuAccessMatrixTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    /**
     * @return array<string, array<int, string>>
     */
    private function menuRouteMatrix(): array
    {
        return [
            'dashboard' => ['super-admin', 'admin', 'kasir', 'pimpinan', 'karyawan'],
            'pelanggan.index' => ['super-admin', 'admin', 'kasir', 'pimpinan'],
            'karyawan.index' => ['super-admin', 'admin', 'pimpinan'],
            'layanan.index' => ['super-admin', 'admin'],
            'produk.index' => ['super-admin', 'admin', 'kasir'],
            'pembelian.index' => ['super-admin', 'admin', 'kasir'],
            'inventaris.index' => ['super-admin', 'admin'],
            'transaksi.index' => ['super-admin', 'admin', 'kasir'],
            'pengeluaran.index' => ['super-admin', 'admin', 'kasir'],
            'absensi.index' => ['super-admin', 'admin', 'kasir', 'karyawan'],
            'absensi.scan' => ['super-admin', 'admin', 'kasir', 'karyawan'],
            'absensi.my-attendance' => ['super-admin', 'admin', 'kasir', 'karyawan'],
            'absensi.monitoring' => ['super-admin', 'admin'],
            'absensi.barcodes.index' => ['super-admin', 'admin'],
            'gaji.index' => ['super-admin', 'admin', 'pimpinan'],
            'laporan.index' => ['super-admin', 'admin', 'pimpinan'],
            'laporan.laba-rugi' => ['super-admin', 'admin', 'pimpinan'],
            'pengaturan.index' => ['super-admin', 'admin'],
            'users.index' => ['super-admin'],
            'password.change' => ['super-admin', 'admin', 'kasir', 'pimpinan', 'karyawan'],
        ];
    }

    public function test_menu_routes_follow_role_access_matrix(): void
    {
        $roles = ['super-admin', 'admin', 'kasir', 'pimpinan', 'karyawan'];

        foreach ($roles as $roleSlug) {
            $user = $this->createUserWithRole($roleSlug);
            $this->ensureKaryawanProfile($user);

            foreach ($this->menuRouteMatrix() as $routeName => $allowedRoles) {
                $response = $this
                    ->actingAs($user)
                    ->from('/dashboard')
                    ->get(route($routeName));

                if (in_array($roleSlug, $allowedRoles, true)) {
                    $response->assertOk(
                        sprintf('Role "%s" should access route "%s".', $roleSlug, $routeName)
                    );
                } else {
                    $response->assertRedirect('/dashboard');
                    $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
                }
            }
        }
    }

    private function ensureKaryawanProfile(User $user): void
    {
        if ($user->karyawan()->exists()) {
            return;
        }

        Karyawan::create([
            'nama' => $user->nama,
            'jabatan' => 'Staff Test',
            'tanggal_bergabung' => now()->toDateString(),
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1200000,
            'user_id' => $user->id,
            'cabang_id' => $user->cabang_id,
        ]);
    }
}
