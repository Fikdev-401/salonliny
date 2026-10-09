<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_nota',
        'tanggal',
        'jenis_pembelian',
        'produk_id',
        'inventaris_id',
        'jumlah',
        'harga_satuan',
        'total',
        'supplier',
        'no_hp_supplier',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(Inventaris::class);
    }

    public function pengeluaran(): HasOne
    {
        return $this->hasOne(Pengeluaran::class, 'sumber_id')
            ->where('sumber_tipe', self::class);
    }
}
