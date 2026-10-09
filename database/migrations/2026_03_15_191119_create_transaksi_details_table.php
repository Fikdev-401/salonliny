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
        Schema::create('transaksi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade');
            
            // Tipe item: layanan atau produk
            $table->enum('tipe', ['layanan', 'produk']);
            $table->foreignId('layanan_id')->nullable()->constrained('layanans')->onDelete('restrict');
            $table->foreignId('produk_id')->nullable()->constrained('produks')->onDelete('restrict');
            
            // Detail item
            $table->string('nama_item');
            $table->integer('jumlah')->default(1);
            $table->string('satuan')->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total', 15, 2);
            
            // Multi-pekerja untuk layanan
            // Pekerja utama
            $table->foreignId('karyawan_utama_id')->nullable()->constrained('karyawans')->onDelete('restrict');
            $table->decimal('insentif_utama', 15, 2)->default(0);
            
            // Pekerja pendukung 1
            $table->foreignId('karyawan_pendukung_1_id')->nullable()->constrained('karyawans')->onDelete('restrict');
            $table->decimal('insentif_pendukung_1', 15, 2)->default(0);
            
            // Pekerja pendukung 2
            $table->foreignId('karyawan_pendukung_2_id')->nullable()->constrained('karyawans')->onDelete('restrict');
            $table->decimal('insentif_pendukung_2', 15, 2)->default(0);
            
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_details');
    }
};
