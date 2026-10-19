<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingVideo extends Model
{
    use HasFactory;

    protected $table = 'landing_videos';

    protected $fillable = [
        'judul',
        'deskripsi',
        'youtube_id',
        'video_file',
        'thumbnail',
        'tipe',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Parse YouTube URL and return the video ID.
     * Supports:
     *   - https://www.youtube.com/watch?v=VIDEO_ID
     *   - https://youtu.be/VIDEO_ID
     *   - https://www.youtube.com/embed/VIDEO_ID
     */
    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        // Already a video ID (no slashes/dots, length 11)
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
            return $url;
        }

        // watch?v=ID
        if (preg_match('/[?&]v=([A-Za-z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }

        // youtu.be/ID
        if (preg_match('#youtu\.be/([A-Za-z0-9_-]{11})#', $url, $m)) {
            return $m[1];
        }

        // embed/ID
        if (preg_match('#embed/([A-Za-z0-9_-]{11})#', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->tipe === 'youtube' && $this->youtube_id) {
            return 'https://www.youtube.com/embed/' . $this->youtube_id;
        }
        return null;
    }

    public function getVideoFileUrlAttribute(): ?string
    {
        if ($this->tipe === 'upload' && $this->video_file) {
            return asset('storage/' . $this->video_file);
        }
        return null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        if ($this->youtube_id) {
            return 'https://img.youtube.com/vi/' . $this->youtube_id . '/hqdefault.jpg';
        }
        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
