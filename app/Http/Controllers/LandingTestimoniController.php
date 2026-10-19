<?php

namespace App\Http\Controllers;

use App\Models\LandingTestimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingTestimoniController extends Controller
{
    public function index()
    {
        $testimonis = LandingTestimoni::orderBy('urutan')->orderBy('id', 'desc')->get();
        return view('landing-admin.testimonis.index', compact('testimonis'));
    }

    public function create()
    {
        return view('landing-admin.testimonis.form', ['testimoni' => new LandingTestimoni()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'pesan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/testimonis', $filename, 'public');
            $validated['foto'] = 'landing/testimonis/' . $filename;
        }

        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        LandingTestimoni::create($validated);

        return redirect()->route('landing-admin.testimonis.index')
            ->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit(LandingTestimoni $testimoni)
    {
        return view('landing-admin.testimonis.form', compact('testimoni'));
    }

    public function update(Request $request, LandingTestimoni $testimoni)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'pesan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('foto')) {
            $this->deleteStorageFile($testimoni->foto);
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('landing/testimonis', $filename, 'public');
            $validated['foto'] = 'landing/testimonis/' . $filename;
        }

        $validated['is_active'] = $request->boolean('is_active', false);

        $testimoni->update($validated);

        return redirect()->route('landing-admin.testimonis.index')
            ->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(LandingTestimoni $testimoni)
    {
        $this->deleteStorageFile($testimoni->foto);
        $testimoni->delete();

        return redirect()->route('landing-admin.testimonis.index')
            ->with('success', 'Testimoni berhasil dihapus.');
    }

    private function deleteStorageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
