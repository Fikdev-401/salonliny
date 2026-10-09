<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gaji extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'gaji_pokok',
        'total_insentif',
        'bonus',
        'thr',
        'potongan',
        'total_gaji',
        'jumlah_hadir',
        'jumlah_izin',
        'jumlah_sakit',
        'jumlah_alpha',
        'status',
        'diajukan_oleh',
        'disetujui_oleh',
        'disetujui_at',
        'tanggal_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'gaji_pokok' => 'decimal:2',
        'total_insentif' => 'decimal:2',
        'bonus' => 'decimal:2',
        'thr' => 'decimal:2',
        'potongan' => 'decimal:2',
        'total_gaji' => 'decimal:2',
        'jumlah_hadir' => 'integer',
        'jumlah_izin' => 'integer',
        'jumlah_sakit' => 'integer',
        'jumlah_alpha' => 'integer',
        'disetujui_at' => 'datetime',
        'tanggal_bayar' => 'date',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function diajukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function pengeluaran(): HasOne
    {
        return $this->hasOne(Pengeluaran::class, 'sumber_id')
            ->where('sumber_tipe', self::class);
    }
}
