<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gajis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('restrict');
            $table->string('periode'); // format: YYYY-MM
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            
            // Komponen gaji
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('total_insentif', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('thr', 15, 2)->default(0); // Tunjangan Hari Raya
            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('total_gaji', 15, 2)->default(0);
            
            // Detail absensi
            $table->integer('jumlah_hadir')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_alpha')->default(0);
            
            // Status
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'dibayar'])->default('draft');
            $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('disetujui_at')->nullable();
            $table->date('tanggal_bayar')->nullable();
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['karyawan_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gajis');
    }
};
