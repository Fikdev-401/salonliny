<?php

namespace App\Models;

use App\Models\Concerns\HasCabangScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory, HasCabangScope;

    protected $fillable = [
        'nama',
        'jabatan',
        'tanggal_lahir',
        'tanggal_bergabung',
        'no_hp',
        'alamat',
        'jenis_kelamin',
        'status_kerja',
        'gaji_pokok',
        'user_id',
        'cabang_id',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
        'gaji_pokok' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function transaksiDetailsUtama(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'karyawan_utama_id');
    }

    public function transaksiDetailsPendukung1(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'karyawan_pendukung_1_id');
    }

    public function transaksiDetailsPendukung2(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'karyawan_pendukung_2_id');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function gajis(): HasMany
    {
        return $this->hasMany(Gaji::class);
    }

    public function riwayatGajis(): HasMany
    {
        return $this->hasMany(RiwayatGajiKaryawan::class)->orderByDesc('berlaku_mulai');
    }

    public function getMasaKerjaAttribute()
    {
        return $this->tanggal_bergabung->diffForHumans();
    }

    public function resolveGajiPokokForDate($date): ?RiwayatGajiKaryawan
    {
        return $this->riwayatGajis()
            ->whereDate('berlaku_mulai', '<=', $date)
            ->first();
    }
}
