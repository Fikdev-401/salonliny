<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pembelians')) {
            if (!Schema::hasColumn('pembelians', 'jenis_pembelian')) {
                Schema::table('pembelians', function (Blueprint $table) {
                    $table->enum('jenis_pembelian', ['produk_jual', 'bahan_pakai', 'alat_inventaris', 'barang_sewa'])
                        ->default('produk_jual')
                        ->after('tanggal');
                });
            }

            if (Schema::hasTable('inventaris') && !Schema::hasColumn('pembelians', 'inventaris_id')) {
                Schema::table('pembelians', function (Blueprint $table) {
                    $table->foreignId('inventaris_id')
                        ->nullable()
                        ->after('produk_id')
                        ->constrained('inventaris')
                        ->nullOnDelete();
                });
            }

            if (Schema::hasColumn('pembelians', 'jenis_pembelian')) {
                DB::table('pembelians')
                    ->whereNull('jenis_pembelian')
                    ->update(['jenis_pembelian' => 'produk_jual']);
            }
        }

        if (Schema::hasTable('pengeluarans')) {
            if (!Schema::hasColumn('pengeluarans', 'sumber_tipe')) {
                Schema::table('pengeluarans', function (Blueprint $table) {
                    $table->string('sumber_tipe')->nullable()->after('user_id');
                });
            }

            if (!Schema::hasColumn('pengeluarans', 'sumber_id')) {
                Schema::table('pengeluarans', function (Blueprint $table) {
                    $table->unsignedBigInteger('sumber_id')->nullable()->after('sumber_tipe');
                });
            }

            if (!Schema::hasColumn('pengeluarans', 'is_system_generated')) {
                Schema::table('pengeluarans', function (Blueprint $table) {
                    $table->boolean('is_system_generated')->default(false)->after('sumber_id');
                });
            }

            if (
                Schema::hasColumns('pengeluarans', ['sumber_tipe', 'sumber_id'])
                && !Schema::hasIndex('pengeluarans', ['sumber_tipe', 'sumber_id'])
            ) {
                Schema::table('pengeluarans', function (Blueprint $table) {
                    $table->index(['sumber_tipe', 'sumber_id']);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropIndex(['sumber_tipe', 'sumber_id']);
            $table->dropColumn(['sumber_tipe', 'sumber_id', 'is_system_generated']);
        });

        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inventaris_id');
            $table->dropColumn('jenis_pembelian');
        });
    }
};
