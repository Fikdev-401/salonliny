<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pembelians') || !Schema::hasColumn('pembelians', 'produk_id') || $this->isProdukIdNullable()) {
            return;
        }

        if ($this->hasProdukIdForeignKey()) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->dropForeign(['produk_id']);
            });
        }

        Schema::table('pembelians', function (Blueprint $table) {
            $table->foreignId('produk_id')->nullable()->change();
        });

        if (!$this->hasProdukIdForeignKey()) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->foreign('produk_id')->references('id')->on('produks')->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('pembelians') || !Schema::hasColumn('pembelians', 'produk_id') || !$this->isProdukIdNullable()) {
            return;
        }

        if ($this->hasProdukIdForeignKey()) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->dropForeign(['produk_id']);
            });
        }

        Schema::table('pembelians', function (Blueprint $table) {
            $table->foreignId('produk_id')->nullable(false)->change();
        });

        if (!$this->hasProdukIdForeignKey()) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->foreign('produk_id')->references('id')->on('produks')->restrictOnDelete();
            });
        }
    }

    private function isProdukIdNullable(): bool
    {
        foreach (Schema::getColumns('pembelians') as $column) {
            if (($column['name'] ?? null) === 'produk_id') {
                return (bool) ($column['nullable'] ?? false);
            }
        }

        return false;
    }

    private function hasProdukIdForeignKey(): bool
    {
        foreach (Schema::getForeignKeys('pembelians') as $foreignKey) {
            if (($foreignKey['columns'] ?? []) === ['produk_id']) {
                return true;
            }
        }

        return false;
    }
};
