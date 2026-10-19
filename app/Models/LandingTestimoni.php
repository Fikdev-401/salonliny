<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingTestimoni extends Model
{
    use HasFactory;

    protected $table = 'landing_testimonis';

    protected $fillable = [
        'nama',
        'jabatan',
        'rating',
        'pesan',
        'foto',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'rating' => 'integer',
    ];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }
        return asset('storage/' . $this->foto);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
