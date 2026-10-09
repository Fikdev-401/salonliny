<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layanan;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $query = Layanan::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $layanans = $query->orderBy('nama')->paginate(20);
        $kategoris = Layanan::distinct()->pluck('kategori')->filter();

        return view('layanan.index', compact('layanans', 'kategoris'));
    }

    public function create()
    {
        return view('layanan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_fleksibel' => 'boolean',
            'harga_default' => 'required|numeric|min:0',
            'durasi_menit' => 'nullable|integer|min:1',
            'kategori' => 'nullable|string|max:100',
        ]);

        $validated['harga_fleksibel'] = $request->boolean('harga_fleksibel');
        $validated['status'] = 'aktif';

        Layanan::create($validated);

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function show(Layanan $layanan)
    {
        return view('layanan.show', compact('layanan'));
    }

    public function edit(Layanan $layanan)
    {
        return view('layanan.edit', compact('layanan'));
    }

    public function update(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_fleksibel' => 'boolean',
            'harga_default' => 'required|numeric|min:0',
            'durasi_menit' => 'nullable|integer|min:1',
            'kategori' => 'nullable|string|max:100',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['harga_fleksibel'] = $request->boolean('harga_fleksibel');

        $layanan->update($validated);

        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Layanan $layanan)
    {
        $layanan->update(['status' => 'nonaktif']);
        return redirect()->route('layanan.index')
            ->with('success', 'Layanan berhasil dinonaktifkan.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        $layanans = Layanan::where('status', 'aktif')
            ->where('nama', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($layanans);
    }
}
