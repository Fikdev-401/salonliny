<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'no_telepon',
        'email',
        'status',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }

    public static function getDefaultCabang()
    {
        return self::where('is_default', true)->where('status', 'aktif')->first();
    }

    public static function getActiveCabangs()
    {
        return self::where('status', 'aktif')->get();
    }
}
