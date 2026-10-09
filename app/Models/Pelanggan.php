<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'no_hp',
        'tanggal_lahir',
        'alamat',
        'jenis_kelamin',
        'total_kunjungan',
        'terakhir_kunjungan',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'terakhir_kunjungan' => 'date',
    ];

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }
}
