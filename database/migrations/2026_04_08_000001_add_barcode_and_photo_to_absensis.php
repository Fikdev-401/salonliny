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
        Schema::table('absensis', function (Blueprint $table) {
            // Barcode reference
            $table->string('barcode')->nullable()->after('keterangan');
            $table->index('barcode');
            
            // Photo paths
            $table->string('foto_masuk')->nullable()->after('barcode');
            $table->string('foto_keluar')->nullable()->after('foto_masuk');
            
            // Location coordinates
            $table->decimal('latitude_masuk', 10, 8)->nullable()->after('foto_keluar');
            $table->decimal('longitude_masuk', 11, 8)->nullable()->after('latitude_masuk');
            $table->decimal('latitude_keluar', 10, 8)->nullable()->after('longitude_masuk');
            $table->decimal('longitude_keluar', 11, 8)->nullable()->after('latitude_keluar');
            
            // Device info
            $table->string('ip_address', 45)->nullable()->after('longitude_keluar');
            $table->text('user_agent')->nullable()->after('ip_address');
            
            // Verification status
            $table->enum('status_verifikasi', ['pending', 'terverifikasi', 'ditolak'])->default('pending')->after('user_agent');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            $table->foreignId('diverifikasi_oleh')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
            $table->dropColumn([
                'barcode',
                'foto_masuk',
                'foto_keluar',
                'latitude_masuk',
                'longitude_masuk',
                'latitude_keluar',
                'longitude_keluar',
                'ip_address',
                'user_agent',
                'status_verifikasi',
                'catatan_verifikasi',
            ]);
        });
    }
};
