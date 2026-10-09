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
        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            // harga_fleksibel = true: harga bisa diubah saat transaksi
            // harga_fleksibel = false: harga tetap sesuai harga_default
            $table->boolean('harga_fleksibel')->default(false);
            $table->decimal('harga_default', 15, 2)->default(0);
            $table->decimal('insentif_karyawan', 15, 2)->default(0);
            $table->integer('durasi_menit')->nullable();
            $table->string('kategori')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};
