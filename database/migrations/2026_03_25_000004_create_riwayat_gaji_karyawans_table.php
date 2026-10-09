<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('riwayat_gaji_karyawans')) {
            Schema::create('riwayat_gaji_karyawans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
                $table->date('berlaku_mulai');
                $table->decimal('gaji_pokok', 15, 2);
                $table->text('catatan')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['karyawan_id', 'berlaku_mulai']);
            });
        }

        $karyawans = DB::table('karyawans')->get();

        foreach ($karyawans as $karyawan) {
            DB::table('riwayat_gaji_karyawans')->updateOrInsert(
                [
                    'karyawan_id' => $karyawan->id,
                    'berlaku_mulai' => $karyawan->tanggal_bergabung ?? now()->toDateString(),
                ],
                [
                    'gaji_pokok' => $karyawan->gaji_pokok ?? 0,
                    'catatan' => 'Migrasi awal dari data karyawan.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_gaji_karyawans');
    }
};
