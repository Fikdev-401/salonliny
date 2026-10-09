<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konfigurasi extends Model
{
    use HasFactory;

    protected $table = 'konfigurasis';

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'no_telepon',
        'email',
        'logo',
        'jam_buka',
        'jam_tutup',
        'pajak_persen',
    ];

    protected $casts = [
        'pajak_persen' => 'decimal:2',
    ];

    // Singleton pattern - hanya ada satu record
    public static function getInstance()
    {
        $config = self::first();
        if (!$config) {
            $config = self::create([
                'nama_perusahaan' => 'Salon & Barber Shop',
            ]);
        }
        return $config;
    }
}
