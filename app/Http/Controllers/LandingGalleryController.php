<?php

namespace App\Http\Controllers;

use App\Models\LandingGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingGalleryController extends Controller
{
    public function index()
    {
        $galleries = LandingGallery::orderBy('urutan')->orderBy('id', 'desc')->get();
        return view('landing-admin.gallery.index', compact('galleries'));
    }

    public function create()
    {
        return view('landing-admin.gallery.form', ['gallery' => new LandingGallery()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/gallery', $filename, 'public');
            $validated['image_path'] = 'landing/gallery/' . $filename;
        }

        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        LandingGallery::create($validated);

        return redirect()->route('landing-admin.gallery.index')
            ->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function edit(LandingGallery $gallery)
    {
        return view('landing-admin.gallery.form', compact('gallery'));
    }

    public function update(Request $request, LandingGallery $gallery)
    {
        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteStorageFile($gallery->image_path);
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/gallery', $filename, 'public');
            $validated['image_path'] = 'landing/gallery/' . $filename;
        }

        $validated['is_active'] = $request->boolean('is_active', false);

        $gallery->update($validated);

        return redirect()->route('landing-admin.gallery.index')
            ->with('success', 'Foto galeri berhasil diperbarui.');
    }

    public function destroy(LandingGallery $gallery)
    {
        $this->deleteStorageFile($gallery->image_path);
        $gallery->delete();

        return redirect()->route('landing-admin.gallery.index')
            ->with('success', 'Foto galeri berhasil dihapus.');
    }

    private function deleteStorageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
