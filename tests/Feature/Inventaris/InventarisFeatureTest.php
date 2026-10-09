<?php

namespace Tests\Feature\Inventaris;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class InventarisFeatureTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_admin_can_manage_inventaris_and_access_report(): void
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->post('/inventaris', [
            'kode_aset' => 'AST-TEST-001',
            'nama_aset' => 'Hair Dryer',
            'jenis_aset' => 'Elektronik',
            'jumlah' => 2,
            'satuan' => 'unit',
            'tanggal_input' => now()->toDateString(),
            'status' => 'bagus',
            'keterangan' => 'Untuk layanan styling',
        ])->assertRedirect('/inventaris');

        $this->assertDatabaseHas('inventaris', [
            'kode_aset' => 'AST-TEST-001',
            'nama_aset' => 'Hair Dryer',
        ]);

        $this->actingAs($admin)->get('/inventaris')->assertOk();
        $this->actingAs($admin)->get('/laporan/inventaris')->assertOk();
    }
}
