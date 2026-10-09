<?php

namespace App\Models;

use App\Models\Concerns\HasCabangScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, HasCabangScope, SoftDeletes;

    protected $fillable = [
        'nomor_transaksi',
        'tanggal_transaksi',
        'pelanggan_id',
        'kasir_id',
        'cabang_id',
        'subtotal',
        'diskon',
        'total',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'status_pembayaran',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function getJumlahItemAttribute()
    {
        return $this->details->sum('jumlah');
    }

    public function getTotalInsentifAttribute(): float
    {
        return (float) $this->details->sum(function ($detail) {
            return (float) ($detail->insentif_utama ?? 0)
                + (float) ($detail->insentif_pendukung_1 ?? 0)
                + (float) ($detail->insentif_pendukung_2 ?? 0);
        });
    }
}
