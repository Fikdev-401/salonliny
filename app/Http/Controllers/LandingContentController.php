<?php

namespace App\Http\Controllers;

use App\Models\Konfigurasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingContentController extends Controller
{
    public function index()
    {
        $konfigurasi = Konfigurasi::getInstance();
        return view('landing-admin.content', compact('konfigurasi'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Hero
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_cta_message' => 'nullable|string',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            // About
            'about_title' => 'nullable|string|max:255',
            'about_text' => 'nullable|string',
            'about_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            // Kontak & social
            'whatsapp_number' => 'nullable|string|max:30',
            'instagram_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'maps_embed_url' => 'nullable|string',
            // Basic konfigurasi
            'nama_perusahaan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'jam_buka' => 'nullable|string|max:20',
            'jam_tutup' => 'nullable|string|max:20',
        ]);

        $konfigurasi = Konfigurasi::getInstance();

        // Hero image
        if ($request->hasFile('hero_image')) {
            $this->deleteStorageFile($konfigurasi->hero_image);
            $filename = 'hero_' . time() . '.' . $request->file('hero_image')->getClientOriginalExtension();
            $request->file('hero_image')->storeAs('landing/content', $filename, 'public');
            $validated['hero_image'] = 'landing/content/' . $filename;
        }

        // About image
        if ($request->hasFile('about_image')) {
            $this->deleteStorageFile($konfigurasi->about_image);
            $filename = 'about_' . time() . '.' . $request->file('about_image')->getClientOriginalExtension();
            $request->file('about_image')->storeAs('landing/content', $filename, 'public');
            $validated['about_image'] = 'landing/content/' . $filename;
        }

        $konfigurasi->update($validated);

        return redirect()->route('landing-admin.content')
            ->with('success', 'Konten landing page berhasil diperbarui.');
    }

    private function deleteStorageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
