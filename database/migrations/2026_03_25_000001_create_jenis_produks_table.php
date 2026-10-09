<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jenis_produks')) {
            Schema::create('jenis_produks', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('slug')->unique();
                $table->boolean('is_transactable')->default(false);
                $table->boolean('affects_income')->default(false);
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamps();
            });
        }

        foreach ([
            [
                'nama' => 'Produk Jual',
                'slug' => 'produk-jual',
                'is_transactable' => true,
                'affects_income' => true,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Bahan Pakai',
                'slug' => 'bahan-pakai',
                'is_transactable' => false,
                'affects_income' => false,
                'status' => 'aktif',
            ],
            [
                'nama' => 'Barang Sewa',
                'slug' => 'barang-sewa',
                'is_transactable' => true,
                'affects_income' => true,
                'status' => 'aktif',
            ],
        ] as $jenisProduk) {
            $query = DB::table('jenis_produks')->where('slug', $jenisProduk['slug']);

            if ($query->exists()) {
                $query->update(array_merge($jenisProduk, [
                    'updated_at' => now(),
                ]));
            } else {
                DB::table('jenis_produks')->insert(array_merge($jenisProduk, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_produks');
    }
};
