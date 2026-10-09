<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gajis')) {
            return;
        }

        Schema::table('gajis', function (Blueprint $table) {
            if (!Schema::hasColumn('gajis', 'periode')) {
                $table->string('periode')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'gaji_pokok')) {
                $table->decimal('gaji_pokok', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('gajis', 'total_insentif')) {
                $table->decimal('total_insentif', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('gajis', 'bonus')) {
                $table->decimal('bonus', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('gajis', 'thr')) {
                $table->decimal('thr', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('gajis', 'potongan')) {
                $table->decimal('potongan', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('gajis', 'total_gaji')) {
                $table->decimal('total_gaji', 15, 2)->default(0);
            }

            if (!Schema::hasColumn('gajis', 'jumlah_hadir')) {
                $table->integer('jumlah_hadir')->default(0);
            }

            if (!Schema::hasColumn('gajis', 'jumlah_izin')) {
                $table->integer('jumlah_izin')->default(0);
            }

            if (!Schema::hasColumn('gajis', 'jumlah_sakit')) {
                $table->integer('jumlah_sakit')->default(0);
            }

            if (!Schema::hasColumn('gajis', 'jumlah_alpha')) {
                $table->integer('jumlah_alpha')->default(0);
            }

            if (!Schema::hasColumn('gajis', 'status')) {
                $table->string('status')->default('draft');
            }

            if (!Schema::hasColumn('gajis', 'diajukan_oleh')) {
                $table->unsignedBigInteger('diajukan_oleh')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'disetujui_oleh')) {
                $table->unsignedBigInteger('disetujui_oleh')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'disetujui_at')) {
                $table->timestamp('disetujui_at')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'tanggal_bayar')) {
                $table->date('tanggal_bayar')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (!Schema::hasColumn('gajis', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        $rows = DB::table('gajis')->select(['id', 'periode', 'tanggal_mulai', 'tanggal_selesai'])->get();

        foreach ($rows as $row) {
            $periode = $row->periode;

            if (empty($periode)) {
                $periode = now()->format('Y-m');
            }

            try {
                $periodeDate = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
            } catch (\Throwable) {
                $periodeDate = now()->startOfMonth();
                $periode = $periodeDate->format('Y-m');
            }

            $payload = [
                'periode' => $periode,
            ];

            if (!$row->tanggal_mulai) {
                $payload['tanggal_mulai'] = $periodeDate->toDateString();
            }

            if (!$row->tanggal_selesai) {
                $payload['tanggal_selesai'] = $periodeDate->copy()->endOfMonth()->toDateString();
            }

            DB::table('gajis')->where('id', $row->id)->update($payload);
        }

        foreach (['gaji_pokok', 'total_insentif', 'bonus', 'thr', 'potongan', 'total_gaji'] as $column) {
            if (Schema::hasColumn('gajis', $column)) {
                DB::table('gajis')->whereNull($column)->update([$column => 0]);
            }
        }

        foreach (['jumlah_hadir', 'jumlah_izin', 'jumlah_sakit', 'jumlah_alpha'] as $column) {
            if (Schema::hasColumn('gajis', $column)) {
                DB::table('gajis')->whereNull($column)->update([$column => 0]);
            }
        }

        if (Schema::hasColumn('gajis', 'status')) {
            DB::table('gajis')->whereNull('status')->update(['status' => 'draft']);
        }
    }

    public function down(): void
    {
        // Forward-only reconciliation migration for production schema drift.
    }
};
