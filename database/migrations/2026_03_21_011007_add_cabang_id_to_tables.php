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
        if (Schema::hasTable('cabangs')) {
            if (Schema::hasTable('users') && !Schema::hasColumn('users', 'cabang_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreignId('cabang_id')->nullable()->after('role_id')->constrained('cabangs')->onDelete('set null');
                });
            }

            if (Schema::hasTable('karyawans') && !Schema::hasColumn('karyawans', 'cabang_id')) {
                Schema::table('karyawans', function (Blueprint $table) {
                    $table->foreignId('cabang_id')->nullable()->after('user_id')->constrained('cabangs')->onDelete('set null');
                });
            }

            if (Schema::hasTable('transaksis') && !Schema::hasColumn('transaksis', 'cabang_id')) {
                Schema::table('transaksis', function (Blueprint $table) {
                    $table->foreignId('cabang_id')->nullable()->after('kasir_id')->constrained('cabangs')->onDelete('set null');
                });
            }

            if (Schema::hasTable('produks') && !Schema::hasColumn('produks', 'cabang_id')) {
                Schema::table('produks', function (Blueprint $table) {
                    $table->foreignId('cabang_id')->nullable()->after('id')->constrained('cabangs')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropForeign(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
    }
};
