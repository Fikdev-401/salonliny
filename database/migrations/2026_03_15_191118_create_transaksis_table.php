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
        if (Schema::hasTable('transaksis')) {
            Schema::table('transaksis', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksis', 'nomor_transaksi')) {
                    $table->string('nomor_transaksi')->unique();
                }
                if (!Schema::hasColumn('transaksis', 'tanggal_transaksi')) {
                    $table->dateTime('tanggal_transaksi')->nullable();
                }
                if (!Schema::hasColumn('transaksis', 'subtotal')) {
                    $table->decimal('subtotal', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('transaksis', 'diskon')) {
                    $table->decimal('diskon', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('transaksis', 'total')) {
                    $table->decimal('total', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('transaksis', 'bayar')) {
                    $table->decimal('bayar', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('transaksis', 'kembalian')) {
                    $table->decimal('kembalian', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('transaksis', 'metode_pembayaran')) {
                    $table->string('metode_pembayaran')->default('tunai');
                }
                if (!Schema::hasColumn('transaksis', 'status_pembayaran')) {
                    $table->string('status_pembayaran')->default('pending');
                }
                if (!Schema::hasColumn('transaksis', 'status')) {
                    $table->string('status')->default('proses');
                }
                if (!Schema::hasColumn('transaksis', 'catatan')) {
                    $table->text('catatan')->nullable();
                }
                if (!Schema::hasColumn('transaksis', 'created_at')) {
                    $table->timestamps();
                }
            });

            return;
        }

        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->dateTime('tanggal_transaksi');
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('restrict');
            $table->foreignId('kasir_id')->constrained('users')->onDelete('restrict');
            
            // Detail pembayaran
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('bayar', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);
            
            // Metode pembayaran
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris', 'debit', 'kredit'])->default('tunai');
            $table->enum('status_pembayaran', ['pending', 'lunas', 'batal'])->default('pending');
            
            // Status transaksi (untuk handle tambahan layanan di tengah jalan)
            $table->enum('status', ['proses', 'selesai', 'batal'])->default('proses');
            
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
