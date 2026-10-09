<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AbsensiPhotoService
{
    /**
     * Disk storage untuk foto absensi
     */
    protected string $disk = 'public';

    /**
     * Base path untuk foto absensi
     */
    protected string $basePath = 'absensi/photos';

    protected int $maxBase64Bytes = 3_145_728;

    /**
     * Simpan foto absensi
     */
    public function savePhoto(UploadedFile $file, string $type, int $karyawanId, Carbon $tanggal): string
    {
        // Validasi type
        if (!in_array($type, ['checkin', 'checkout'])) {
            throw new \InvalidArgumentException('Tipe foto harus checkin atau checkout');
        }

        // Buat path: absensi/photos/checkin/2026-04-08/
        $folder = "{$this->basePath}/{$type}/{$tanggal->format('Y-m-d')}";
        
        // Generate filename: karyawanId_timestamp_random.jpg
        $filename = "{$karyawanId}_" . now()->format('His') . '_' . Str::random(8) . '.jpg';
        
        // Simpan file
        $path = $file->storeAs($folder, $filename, $this->disk);
        
        return $path;
    }

    /**
     * Simpan foto dari base64 (untuk camera capture)
     */
    public function saveBase64Photo(string $base64Data, string $type, int $karyawanId, Carbon $tanggal): string
    {
        // Validasi type
        if (!in_array($type, ['checkin', 'checkout'])) {
            throw new \InvalidArgumentException('Tipe foto harus checkin atau checkout');
        }

        if (!preg_match('#^data:image/(jpeg|jpg|png|webp);base64,#i', $base64Data, $matches)) {
            throw new \InvalidArgumentException('Format foto harus JPG, PNG, atau WEBP');
        }

        $rawPayload = preg_replace('#^data:image/[a-zA-Z0-9.+-]+;base64,#i', '', $base64Data);
        $imageData = base64_decode($rawPayload, true);
        
        if (!$imageData) {
            throw new \InvalidArgumentException('Data base64 tidak valid');
        }

        if (strlen($imageData) > $this->maxBase64Bytes) {
            throw new \InvalidArgumentException('Ukuran foto maksimal 3 MB');
        }

        $imageInfo = @getimagesizefromstring($imageData);
        if (!$imageInfo || !in_array($imageInfo['mime'] ?? '', ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new \InvalidArgumentException('Konten foto tidak valid');
        }

        // Buat path
        $folder = "{$this->basePath}/{$type}/{$tanggal->format('Y-m-d')}";
        $filename = "{$karyawanId}_" . now()->format('His') . '_' . Str::random(8) . '.jpg';
        $path = "{$folder}/{$filename}";
        
        // Simpan file
        Storage::disk($this->disk)->put($path, $imageData);
        
        return $path;
    }

    /**
     * Hapus foto
     */
    public function deletePhoto(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Get URL foto
     */
    public function getPhotoUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Get full path foto
     */
    public function getFullPath(string $path): string
    {
        return Storage::disk($this->disk)->path($path);
    }

    /**
     * Cek apakah foto exists
     */
    public function photoExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return Storage::disk($this->disk)->exists($path);
    }

    /**
     * Resize foto (gunakan Intervention Image jika tersedia)
     */
    public function resizePhoto(string $path, int $width, int $height): bool
    {
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            return false;
        }

        // Jika Intervention Image tersedia
        if (class_exists(\Intervention\Image\Facades\Image::class)) {
            try {
                $image = \Intervention\Image\Facades\Image::make($fullPath);
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image->save($fullPath, 80); // 80% quality
                return true;
            } catch (\Exception $e) {
                \Log::error('Gagal resize foto', ['error' => $e->getMessage()]);
                return false;
            }
        }

        // Fallback: tidak melakukan resize jika Intervention Image tidak tersedia
        return true;
    }

    /**
     * Add watermark ke foto
     */
    public function addWatermark(string $path, string $text): bool
    {
        $fullPath = $this->getFullPath($path);
        
        if (!file_exists($fullPath)) {
            return false;
        }

        if (class_exists(\Intervention\Image\Facades\Image::class)) {
            try {
                $image = \Intervention\Image\Facades\Image::make($fullPath);
                
                // Tambah watermark text
                $image->text($text, 20, $image->height() - 20, function ($font) {
                    $font->size(20);
                    $font->color('#ffffff');
                    $font->stroke('#000000', 2);
                    $font->align('left');
                    $font->valign('bottom');
                });
                
                $image->save($fullPath);
                return true;
            } catch (\Exception $e) {
                \Log::error('Gagal menambah watermark', ['error' => $e->getMessage()]);
                return false;
            }
        }

        return true;
    }

    /**
     * Cleanup foto lama
     */
    public function cleanupOldPhotos(int $daysOld = 90): int
    {
        $deletedCount = 0;
        $cutoffDate = Carbon::now()->subDays($daysOld);
        
        // Scan folder checkin dan checkout
        foreach (['checkin', 'checkout'] as $type) {
            $basePath = "{$this->basePath}/{$type}";
            
            if (!Storage::disk($this->disk)->exists($basePath)) {
                continue;
            }
            
            $folders = Storage::disk($this->disk)->directories($basePath);
            
            foreach ($folders as $folder) {
                // Extract date dari nama folder (format: Y-m-d)
                $folderName = basename($folder);
                $folderDate = Carbon::createFromFormat('Y-m-d', $folderName);
                
                if ($folderDate && $folderDate->lessThan($cutoffDate)) {
                    Storage::disk($this->disk)->deleteDirectory($folder);
                    $deletedCount++;
                }
            }
        }
        
        \Log::info('Cleanup old photos', ['deleted_folders' => $deletedCount, 'days_old' => $daysOld]);
        
        return $deletedCount;
    }
}
