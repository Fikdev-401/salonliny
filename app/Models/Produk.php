<?php

namespace App\Models;

use App\Models\Concerns\HasCabangScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory, HasCabangScope;

    protected $table = 'produks';

    protected $fillable = [
        'cabang_id',
        'jenis_produk_id',
        'nama',
        'kode',
        'deskripsi',
        'kategori',
        'satuan',
        'stok',
        'stok_minimal',
        'harga_jual',
        'harga_beli',
        'harga_fleksibel',
        'status',
    ];

    protected $casts = [
        'stok' => 'integer',
        'stok_minimal' => 'integer',
        'harga_jual' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'harga_fleksibel' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Produk $produk): void {
            if (!empty($produk->jenis_produk_id)) {
                return;
            }

            $produk->jenis_produk_id = JenisProduk::query()
                ->where('slug', 'produk-jual')
                ->value('id');
        });
    }

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }

    public function jenisProduk(): BelongsTo
    {
        return $this->belongsTo(JenisProduk::class);
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function transaksiDetails(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function getStokStatusAttribute()
    {
        if ($this->stok <= 0) {
            return 'habis';
        } elseif ($this->stok <= $this->stok_minimal) {
            return 'menipis';
        }
        return 'tersedia';
    }

    public function scopeForTransaksi($query)
    {
        return $query->whereHas('jenisProduk', function ($jenisQuery) {
            $jenisQuery
                ->where('status', 'aktif')
                ->where('is_transactable', true)
                ->where('affects_income', true);
        });
    }
}
