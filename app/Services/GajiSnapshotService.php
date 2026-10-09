<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\TransaksiDetail;
use Carbon\Carbon;

class GajiSnapshotService
{
    public function buildSnapshot(Karyawan $karyawan, string $periode, array $overrides = []): array
    {
        $periodeDate = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
        $tanggalMulai = $periodeDate->copy()->startOfMonth();
        $tanggalSelesai = $periodeDate->copy()->endOfMonth();
        $riwayatGaji = $karyawan->resolveGajiPokokForDate($tanggalMulai);

        if (!$riwayatGaji) {
            throw new \RuntimeException('Riwayat gaji pokok karyawan belum tersedia untuk periode ini.');
        }

        $totalInsentif = $this->calculateTotalInsentif($karyawan->id, $tanggalMulai, $tanggalSelesai);
        $absensi = $this->calculateAttendance($karyawan->id, $tanggalMulai, $tanggalSelesai);
        $bonus = (float) ($overrides['bonus'] ?? 0);
        $thr = (float) ($overrides['thr'] ?? 0);
        $potongan = (float) ($overrides['potongan'] ?? 0);
        $gajiPokok = (float) $riwayatGaji->gaji_pokok;

        return [
            'periode' => $periode,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'gaji_pokok' => $gajiPokok,
            'total_insentif' => $totalInsentif,
            'base_total' => $gajiPokok + $totalInsentif,
            'bonus' => $bonus,
            'thr' => $thr,
            'potongan' => $potongan,
            'total_gaji' => $gajiPokok + $totalInsentif + $bonus + $thr - $potongan,
            'jumlah_hadir' => $absensi['hadir'],
            'jumlah_izin' => $absensi['izin'],
            'jumlah_sakit' => $absensi['sakit'],
            'jumlah_alpha' => $absensi['alpha'],
        ];
    }

    public function calculateTotalInsentif(int $karyawanId, Carbon $tanggalMulai, Carbon $tanggalSelesai): float
    {
        return (float) TransaksiDetail::query()
            ->whereHas('transaksi', function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereNull('deleted_at')
                    ->whereBetween('tanggal_transaksi', [$tanggalMulai, $tanggalSelesai])
                    ->where('status', '!=', 'batal');
            })
            ->where(function ($query) use ($karyawanId) {
                $query->where('karyawan_utama_id', $karyawanId)
                    ->orWhere('karyawan_pendukung_1_id', $karyawanId)
                    ->orWhere('karyawan_pendukung_2_id', $karyawanId);
            })
            ->get()
            ->sum(function (TransaksiDetail $detail) use ($karyawanId) {
                $total = 0;

                if ((int) $detail->karyawan_utama_id === $karyawanId) {
                    $total += (float) $detail->insentif_utama;
                }

                if ((int) $detail->karyawan_pendukung_1_id === $karyawanId) {
                    $total += (float) $detail->insentif_pendukung_1;
                }

                if ((int) $detail->karyawan_pendukung_2_id === $karyawanId) {
                    $total += (float) $detail->insentif_pendukung_2;
                }

                return $total;
            });
    }

    public function calculateAttendance(int $karyawanId, Carbon $tanggalMulai, Carbon $tanggalSelesai): array
    {
        $absensiData = Absensi::where('karyawan_id', $karyawanId)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->get();

        return [
            'hadir' => $absensiData->where('status', 'hadir')->count(),
            'izin' => $absensiData->where('status', 'izin')->count(),
            'sakit' => $absensiData->where('status', 'sakit')->count(),
            'alpha' => $absensiData->where('status', 'alpha')->count(),
        ];
    }
}
