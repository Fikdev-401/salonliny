<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
        'barcode',
        'foto_masuk',
        'foto_keluar',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'lokasi_missing',
        'ip_address',
        'user_agent',
        'status_verifikasi',
        'catatan_verifikasi',
        'diverifikasi_oleh',
        'diverifikasi_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
        'lokasi_missing' => 'boolean',
        'diverifikasi_at' => 'datetime',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function getDurasiKerjaAttribute()
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = \Carbon\Carbon::parse($this->jam_masuk);
            $keluar = \Carbon\Carbon::parse($this->jam_keluar);
            return $masuk->diffInHours($keluar) . ' jam ' . ($masuk->diffInMinutes($keluar) % 60) . ' menit';
        }
        return '-';
    }

    /**
     * Get URL foto masuk
     */
    public function getFotoMasukUrlAttribute(): ?string
    {
        if ($this->foto_masuk) {
            return asset('storage/' . $this->foto_masuk);
        }
        return null;
    }

    /**
     * Get URL foto keluar
     */
    public function getFotoKeluarUrlAttribute(): ?string
    {
        if ($this->foto_keluar) {
            return asset('storage/' . $this->foto_keluar);
        }
        return null;
    }

    /**
     * Get lokasi masuk dalam format string
     */
    public function getLokasiMasukAttribute(): ?string
    {
        if ($this->latitude_masuk && $this->longitude_masuk) {
            return "{$this->latitude_masuk}, {$this->longitude_masuk}";
        }
        return null;
    }

    /**
     * Get lokasi keluar dalam format string
     */
    public function getLokasiKeluarAttribute(): ?string
    {
        if ($this->latitude_keluar && $this->longitude_keluar) {
            return "{$this->latitude_keluar}, {$this->longitude_keluar}";
        }
        return null;
    }

    /**
     * Get status verifikasi label
     */
    public function getStatusVerifikasiLabelAttribute(): string
    {
        return match($this->status_verifikasi) {
            'terverifikasi' => '<span class="badge bg-success">Terverifikasi</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            default => '<span class="badge bg-warning">Pending</span>',
        };
    }

    /**
     * Scope untuk absensi hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', \Carbon\Carbon::today());
    }

    /**
     * Scope untuk absensi yang belum diverifikasi
     */
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'pending');
    }

    /**
     * Scope untuk absensi dengan foto
     */
    public function scopeWithPhoto($query)
    {
        return $query->whereNotNull('foto_masuk');
    }
}
