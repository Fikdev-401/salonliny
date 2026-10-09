<?php

namespace App\Services;

use App\Models\Gaji;
use Illuminate\Support\Facades\Log;

class GajiDraftSyncService
{
    public function __construct(
        private readonly GajiSnapshotService $gajiSnapshotService
    ) {
    }

    public function sync(array $targets): void
    {
        $uniqueTargets = collect($targets)
            ->filter(fn ($target) => !empty($target['karyawan_id']) && !empty($target['periode']))
            ->unique(fn ($target) => $target['karyawan_id'] . '|' . $target['periode'])
            ->values();

        foreach ($uniqueTargets as $target) {
            $gaji = Gaji::with('karyawan.riwayatGajis')
                ->where('status', 'draft')
                ->where('karyawan_id', $target['karyawan_id'])
                ->where('periode', $target['periode'])
                ->first();

            if (!$gaji || !$gaji->karyawan) {
                continue;
            }

            try {
                $snapshot = $this->gajiSnapshotService->buildSnapshot($gaji->karyawan, $gaji->periode, [
                    'bonus' => $gaji->bonus,
                    'thr' => $gaji->thr,
                    'potongan' => $gaji->potongan,
                ]);

                $gaji->update([
                    'tanggal_mulai' => $snapshot['tanggal_mulai'],
                    'tanggal_selesai' => $snapshot['tanggal_selesai'],
                    'gaji_pokok' => $snapshot['gaji_pokok'],
                    'total_insentif' => $snapshot['total_insentif'],
                    'bonus' => $snapshot['bonus'],
                    'thr' => $snapshot['thr'],
                    'potongan' => $snapshot['potongan'],
                    'total_gaji' => $snapshot['total_gaji'],
                    'jumlah_hadir' => $snapshot['jumlah_hadir'],
                    'jumlah_izin' => $snapshot['jumlah_izin'],
                    'jumlah_sakit' => $snapshot['jumlah_sakit'],
                    'jumlah_alpha' => $snapshot['jumlah_alpha'],
                ]);
            } catch (\RuntimeException $exception) {
                Log::warning('Sinkronisasi draft gaji dilewati karena snapshot tidak dapat dibangun.', [
                    'gaji_id' => $gaji->id,
                    'karyawan_id' => $gaji->karyawan_id,
                    'periode' => $gaji->periode,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
