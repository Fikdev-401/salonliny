<?php

namespace App\Services;

use App\Models\AbsensiBarcode;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AbsensiBarcodeService
{
    /**
     * Generate barcode untuk 1 karyawan
     */
    public function generateForKaryawan(int $karyawanId, ?Carbon $tanggal = null, string $tipe = 'checkin', int $createdBy): AbsensiBarcode
    {
        $tanggal = $tanggal ?? Carbon::today();
        
        // Cek apakah sudah ada barcode untuk tanggal dan tipe ini
        $existing = AbsensiBarcode::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', $tanggal)
            ->where('tipe', $tipe)
            ->where('is_used', false)
            ->first();
        
        if ($existing) {
            return $existing;
        }
        
        return AbsensiBarcode::create([
            'karyawan_id' => $karyawanId,
            'tanggal' => $tanggal,
            'tipe' => $tipe,
            'created_by' => $createdBy,
            'expires_at' => $tanggal->copy()->endOfDay(),
        ]);
    }

    /**
     * Generate barcode massal untuk banyak karyawan
     */
    public function generateBulk(array $karyawanIds, ?Carbon $tanggal = null, int $createdBy): Collection
    {
        $tanggal = $tanggal ?? Carbon::today();
        $barcodes = collect();
        
        foreach ($karyawanIds as $karyawanId) {
            try {
                // Generate check-in barcode
                $barcodeIn = $this->generateForKaryawan($karyawanId, $tanggal, 'checkin', $createdBy);
                $barcodes->push($barcodeIn);
                
                // Generate check-out barcode
                $barcodeOut = $this->generateForKaryawan($karyawanId, $tanggal, 'checkout', $createdBy);
                $barcodes->push($barcodeOut);
            } catch (\Exception $e) {
                Log::error('Gagal generate barcode untuk karyawan', [
                    'karyawan_id' => $karyawanId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return $barcodes;
    }

    /**
     * Generate barcode untuk semua karyawan aktif
     */
    public function generateForAllActiveKaryawan(?Carbon $tanggal = null, int $createdBy): Collection
    {
        $tanggal = $tanggal ?? Carbon::today();
        
        $karyawanIds = Karyawan::where('status_kerja', 'aktif')
            ->pluck('id')
            ->toArray();
        
        return $this->generateBulk($karyawanIds, $tanggal, $createdBy);
    }

    /**
     * Validasi barcode
     */
    public function validateBarcode(string $barcode): array
    {
        $barcodeRecord = AbsensiBarcode::where('barcode', $barcode)->first();
        
        if (!$barcodeRecord) {
            return [
                'valid' => false,
                'message' => 'Barcode tidak ditemukan.',
            ];
        }
        
        if ($barcodeRecord->is_used) {
            return [
                'valid' => false,
                'message' => 'Barcode sudah digunakan.',
            ];
        }
        
        if ($barcodeRecord->isExpired()) {
            return [
                'valid' => false,
                'message' => 'Barcode sudah expired.',
            ];
        }
        
        return [
            'valid' => true,
            'barcode' => $barcodeRecord,
            'message' => 'Barcode valid.',
        ];
    }

    /**
     * Tandai barcode sudah digunakan
     */
    public function markAsUsed(string $barcode): bool
    {
        $barcodeRecord = AbsensiBarcode::where('barcode', $barcode)->first();
        
        if (!$barcodeRecord || !$barcodeRecord->isValid()) {
            return false;
        }
        
        $barcodeRecord->markAsUsed();
        return true;
    }

    /**
     * Get barcode untuk karyawan hari ini
     */
    public function getTodayBarcode(int $karyawanId, string $tipe = 'checkin'): ?AbsensiBarcode
    {
        return AbsensiBarcode::where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', Carbon::today())
            ->where('tipe', $tipe)
            ->first();
    }

    /**
     * Get expired barcodes
     */
    public function getExpiredBarcodes(?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::now();
        
        return AbsensiBarcode::where('expires_at', '<', $date)
            ->where('is_used', false)
            ->get();
    }

    /**
     * Cleanup expired barcodes
     */
    public function cleanupExpiredBarcodes(): int
    {
        $expiredBarcodes = $this->getExpiredBarcodes();
        $count = $expiredBarcodes->count();
        
        foreach ($expiredBarcodes as $barcode) {
            $barcode->delete();
        }
        
        Log::info('Cleanup expired barcodes', ['count' => $count]);
        
        return $count;
    }

    /**
     * Get barcode statistics
     */
    public function getStatistics(?Carbon $tanggal = null): array
    {
        $tanggal = $tanggal ?? Carbon::today();
        
        $total = AbsensiBarcode::whereDate('tanggal', $tanggal)->count();
        $used = AbsensiBarcode::whereDate('tanggal', $tanggal)->where('is_used', true)->count();
        $active = AbsensiBarcode::whereDate('tanggal', $tanggal)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->count();
        $expired = AbsensiBarcode::whereDate('tanggal', $tanggal)
            ->where('is_used', false)
            ->where('expires_at', '<=', now())
            ->count();
        
        return [
            'total' => $total,
            'used' => $used,
            'active' => $active,
            'expired' => $expired,
        ];
    }
}
