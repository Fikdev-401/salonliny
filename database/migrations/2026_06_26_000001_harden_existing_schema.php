<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureUsers();
        $this->ensureCabangs();
        $this->ensureJenisProduks();
        $this->ensureCabangColumns();
        $this->ensureProdukDefaults();
        $this->ensureAbsensiFlags();
    }

    public function down(): void
    {
        // Forward-only hardening migration.
    }

    private function ensureUsers(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'must_change_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('must_change_password')->default(false)->after('status');
            });
        }
    }

    private function ensureCabangs(): void
    {
        if (!Schema::hasTable('cabangs')) {
            Schema::create('cabangs', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->text('alamat')->nullable();
                $table->string('no_telepon')->nullable();
                $table->string('email')->nullable();
                $table->string('status')->default('aktif');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        if (!DB::table('cabangs')->where('is_default', true)->exists()) {
            DB::table('cabangs')->insert([
                'nama' => 'Cabang Utama',
                'alamat' => 'Alamat Cabang Utama',
                'status' => 'aktif',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function ensureJenisProduks(): void
    {
        if (!Schema::hasTable('jenis_produks')) {
            Schema::create('jenis_produks', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('slug')->unique();
                $table->boolean('is_transactable')->default(false);
                $table->boolean('affects_income')->default(false);
                $table->string('status')->default('aktif');
                $table->timestamps();
            });
        }

        foreach ([
            ['nama' => 'Produk Jual', 'slug' => 'produk-jual', 'is_transactable' => true, 'affects_income' => true],
            ['nama' => 'Bahan Pakai', 'slug' => 'bahan-pakai', 'is_transactable' => false, 'affects_income' => false],
            ['nama' => 'Barang Sewa', 'slug' => 'barang-sewa', 'is_transactable' => true, 'affects_income' => true],
        ] as $jenisProduk) {
            DB::table('jenis_produks')->updateOrInsert(
                ['slug' => $jenisProduk['slug']],
                array_merge($jenisProduk, [
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    private function ensureProdukDefaults(): void
    {
        if (!Schema::hasTable('produks')) {
            return;
        }

        if (!Schema::hasColumn('produks', 'jenis_produk_id')) {
            Schema::table('produks', function (Blueprint $table) {
                $table->foreignId('jenis_produk_id')->nullable()->after('cabang_id')->constrained('jenis_produks')->nullOnDelete();
            });
        }

        $produkJualId = DB::table('jenis_produks')->where('slug', 'produk-jual')->value('id');
        if ($produkJualId) {
            DB::table('produks')->whereNull('jenis_produk_id')->update(['jenis_produk_id' => $produkJualId]);
        }
    }

    private function ensureCabangColumns(): void
    {
        $defaultCabangId = DB::table('cabangs')->where('is_default', true)->value('id');

        foreach (['users', 'karyawans', 'transaksis', 'produks'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            if (!Schema::hasColumn($tableName, 'cabang_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->nullOnDelete();
                });
            }

            if ($defaultCabangId) {
                DB::table($tableName)->whereNull('cabang_id')->update(['cabang_id' => $defaultCabangId]);
            }
        }
    }

    private function ensureAbsensiFlags(): void
    {
        if (Schema::hasTable('absensis') && !Schema::hasColumn('absensis', 'lokasi_missing')) {
            Schema::table('absensis', function (Blueprint $table) {
                $table->boolean('lokasi_missing')->default(false)->after('longitude_keluar');
            });
        }
    }
};
