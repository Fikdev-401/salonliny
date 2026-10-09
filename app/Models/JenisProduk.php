<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisProduk extends Model
{
    use HasFactory;

    protected $table = 'jenis_produks';

    protected $fillable = [
        'nama',
        'slug',
        'is_transactable',
        'affects_income',
        'status',
    ];

    protected $casts = [
        'is_transactable' => 'boolean',
        'affects_income' => 'boolean',
    ];

    public function produks(): HasMany
    {
        return $this->hasMany(Produk::class);
    }
}
