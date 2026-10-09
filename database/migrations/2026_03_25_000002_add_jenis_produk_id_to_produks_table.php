<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('produks')
            && Schema::hasTable('jenis_produks')
            && !Schema::hasColumn('produks', 'jenis_produk_id')
        ) {
            Schema::table('produks', function (Blueprint $table) {
                $table->foreignId('jenis_produk_id')
                    ->nullable()
                    ->after('cabang_id')
                    ->constrained('jenis_produks')
                    ->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('produks', 'jenis_produk_id')) {
            return;
        }

        $defaultJenisId = DB::table('jenis_produks')
            ->where('slug', 'produk-jual')
            ->value('id');

        if ($defaultJenisId) {
            DB::table('produks')
                ->whereNull('jenis_produk_id')
                ->update(['jenis_produk_id' => $defaultJenisId]);
        }
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jenis_produk_id');
        });
    }
};
