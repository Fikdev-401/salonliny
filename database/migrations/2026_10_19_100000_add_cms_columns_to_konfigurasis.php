<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('konfigurasis', function (Blueprint $table) {
            // Hero
            $table->string('hero_title')->nullable()->after('pajak_persen');
            $table->text('hero_subtitle')->nullable()->after('hero_title');
            $table->string('hero_image')->nullable()->after('hero_subtitle');
            $table->text('hero_cta_message')->nullable()->after('hero_image');

            // About
            $table->string('about_title')->nullable()->after('hero_cta_message');
            $table->longText('about_text')->nullable()->after('about_title');
            $table->string('about_image')->nullable()->after('about_text');

            // Kontak & social
            $table->string('whatsapp_number', 30)->nullable()->after('about_image');
            $table->string('instagram_url')->nullable()->after('whatsapp_number');
            $table->string('tiktok_url')->nullable()->after('instagram_url');
            $table->string('facebook_url')->nullable()->after('tiktok_url');
            $table->string('youtube_url')->nullable()->after('facebook_url');
            $table->text('maps_embed_url')->nullable()->after('youtube_url');
        });
    }

    public function down(): void
    {
        Schema::table('konfigurasis', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title', 'hero_subtitle', 'hero_image', 'hero_cta_message',
                'about_title', 'about_text', 'about_image',
                'whatsapp_number', 'instagram_url', 'tiktok_url', 'facebook_url', 'youtube_url',
                'maps_embed_url',
            ]);
        });
    }
};
