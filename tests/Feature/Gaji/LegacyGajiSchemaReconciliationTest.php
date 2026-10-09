<?php

namespace Tests\Feature\Gaji;

use App\Models\Karyawan;
use App\Models\RiwayatGajiKaryawan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class LegacyGajiSchemaReconciliationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_legacy_gaji_schema_reconciliation_adds_missing_columns_non_destructively(): void
    {
        $admin = $this->createUserWithRole('admin');
        $karyawan = Karyawan::create([
            'nama' => 'Karyawan Legacy',
            'jabatan' => 'Stylist',
            'tanggal_bergabung' => '2026-01-01',
            'status_kerja' => 'aktif',
            'gaji_pokok' => 1800000,
        ]);

        RiwayatGajiKaryawan::create([
            'karyawan_id' => $karyawan->id,
            'berlaku_mulai' => '2026-04-01',
            'gaji_pokok' => 1800000,
            'created_by' => $admin->id,
        ]);

        Schema::drop('gajis');
        Schema::create('gajis', function ($table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->string('periode')->nullable();
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('total_gaji', 15, 2)->default(0);
            $table->integer('jumlah_hadir')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->string('status')->nullable();
            $table->unsignedBigInteger('diajukan_oleh')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        $migration = require database_path('migrations/2026_04_03_000001_reconcile_legacy_gajis_schema.php');
        $migration->up();

        $this->assertTrue(Schema::hasColumn('gajis', 'tanggal_mulai'));
        $this->assertTrue(Schema::hasColumn('gajis', 'tanggal_selesai'));
        $this->assertTrue(Schema::hasColumn('gajis', 'total_insentif'));
        $this->assertTrue(Schema::hasColumn('gajis', 'thr'));
        $this->assertTrue(Schema::hasColumn('gajis', 'jumlah_sakit'));

        $this->actingAs($admin)->post('/gaji', [
            'karyawan_id' => $karyawan->id,
            'periode' => '2026-04',
            'bonus' => 0,
            'thr' => 0,
            'potongan' => 0,
        ])->assertRedirect('/gaji');

        $this->assertDatabaseHas('gajis', [
            'karyawan_id' => $karyawan->id,
            'periode' => '2026-04',
            'gaji_pokok' => 1800000,
        ]);
    }
}
