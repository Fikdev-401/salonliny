<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventaris')) {
            Schema::create('inventaris', function (Blueprint $table) {
                $table->id();
                $table->string('kode_aset')->unique();
                $table->string('nama_aset');
                $table->string('jenis_aset');
                $table->unsignedInteger('jumlah')->default(1);
                $table->string('satuan')->default('unit');
                $table->date('tanggal_input');
                $table->enum('status', ['bagus', 'rusak'])->default('bagus');
                $table->text('keterangan')->nullable();
                $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
