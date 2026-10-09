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
        if (!Schema::hasTable('pembelians') || Schema::hasColumn('pembelians', 'no_hp_supplier')) {
            return;
        }

        if (Schema::hasColumn('pembelians', 'supplier_hp')) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->renameColumn('supplier_hp', 'no_hp_supplier');
            });

            return;
        }

        Schema::table('pembelians', function (Blueprint $table) {
            $table->string('no_hp_supplier')->nullable()->after('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('pembelians') || Schema::hasColumn('pembelians', 'supplier_hp') || !Schema::hasColumn('pembelians', 'no_hp_supplier')) {
            return;
        }

        Schema::table('pembelians', function (Blueprint $table) {
            $table->renameColumn('no_hp_supplier', 'supplier_hp');
        });
    }
};
