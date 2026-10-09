<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_details';

    protected $fillable = [
        'transaksi_id',
        'tipe',
        'layanan_id',
        'produk_id',
        'nama_item',
        'jumlah',
        'satuan',
        'harga_satuan',
        'total',
        'karyawan_utama_id',
        'insentif_utama',
        'karyawan_pendukung_1_id',
        'insentif_pendukung_1',
        'karyawan_pendukung_2_id',
        'insentif_pendukung_2',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total' => 'decimal:2',
        'insentif_utama' => 'decimal:2',
        'insentif_pendukung_1' => 'decimal:2',
        'insentif_pendukung_2' => 'decimal:2',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    public function karyawanUtama(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_utama_id');
    }

    public function karyawanPendukung1(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pendukung_1_id');
    }

    public function karyawanPendukung2(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_pendukung_2_id');
    }

    public function getTotalInsentifAttribute()
    {
        return $this->insentif_utama + $this->insentif_pendukung_1 + $this->insentif_pendukung_2;
    }
}
