<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AbsensiBarcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'barcode',
        'tanggal',
        'tipe',
        'is_used',
        'used_at',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Boot method untuk generate barcode otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->barcode)) {
                $model->barcode = self::generateUniqueBarcode();
            }
            if (empty($model->expires_at)) {
                $model->expires_at = Carbon::parse($model->tanggal)->endOfDay();
            }
        });
    }

    /**
     * Relasi ke Karyawan
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Relasi ke User (yang membuat barcode)
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate barcode unik
     */
    public static function generateUniqueBarcode(): string
    {
        $prefix = 'ABS';
        $date = Carbon::now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -5));
        
        $barcode = "{$prefix}-{$date}-{$random}";
        
        // Cek duplikat
        while (self::where('barcode', $barcode)->exists()) {
            $random = strtoupper(substr(uniqid(), -5));
            $barcode = "{$prefix}-{$date}-{$random}";
        }
        
        return $barcode;
    }

    /**
     * Cek apakah barcode sudah expired
     */
    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * Cek apakah barcode masih valid
     */
    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Tandai barcode sudah digunakan
     */
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Scope untuk barcode yang masih aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_used', false)
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope untuk barcode hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    /**
     * Scope untuk check-in barcode
     */
    public function scopeCheckIn($query)
    {
        return $query->where('tipe', 'checkin');
    }

    /**
     * Scope untuk check-out barcode
     */
    public function scopeCheckOut($query)
    {
        return $query->where('tipe', 'checkout');
    }

    /**
     * Get formatted barcode untuk display
     */
    public function getFormattedBarcodeAttribute(): string
    {
        return chunk_split($this->barcode, 4, '-');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_used) {
            return '<span class="badge bg-secondary">Digunakan</span>';
        }
        
        if ($this->isExpired()) {
            return '<span class="badge bg-danger">Expired</span>';
        }
        
        return '<span class="badge bg-success">Aktif</span>';
    }
}
