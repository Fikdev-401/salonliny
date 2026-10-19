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
        // CMS landing page
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'hero_cta_message',
        'about_title',
        'about_text',
        'about_image',
        'whatsapp_number',
        'instagram_url',
        'tiktok_url',
        'facebook_url',
        'youtube_url',
        'maps_embed_url',
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

    public function getHeroImageUrlAttribute(): ?string
    {
        if (!$this->hero_image) {
            return null;
        }
        return asset('storage/' . $this->hero_image);
    }

    public function getAboutImageUrlAttribute(): ?string
    {
        if (!$this->about_image) {
            return null;
        }
        return asset('storage/' . $this->about_image);
    }
}
