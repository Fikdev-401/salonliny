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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique()->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable();
            $table->string('satuan')->default('pcs');
            $table->integer('stok')->default(0);
            $table->integer('stok_minimal')->default(5);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->decimal('harga_beli', 15, 2)->default(0);
            // harga_fleksibel = true: harga bisa diubah saat transaksi
            $table->boolean('harga_fleksibel')->default(false);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
