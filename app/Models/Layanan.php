<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanans';

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga_fleksibel',
        'harga_default',
        'insentif_karyawan',
        'durasi_menit',
        'kategori',
        'status',
    ];

    protected $casts = [
        'harga_fleksibel' => 'boolean',
        'harga_default' => 'decimal:2',
        'insentif_karyawan' => 'decimal:2',
    ];

    public function transaksiDetails(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
