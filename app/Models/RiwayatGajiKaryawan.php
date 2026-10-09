<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatGajiKaryawan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_gaji_karyawans';

    protected $fillable = [
        'karyawan_id',
        'berlaku_mulai',
        'gaji_pokok',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'berlaku_mulai' => 'date',
        'gaji_pokok' => 'decimal:2',
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
