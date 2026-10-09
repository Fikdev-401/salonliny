<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::orderBy('created_at', 'desc')->get();
        return view('cabang.index', compact('cabangs'));
    }

    public function create()
    {
        return view('cabang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'is_default' => 'nullable|boolean',
        ]);

        // If this is set as default, unset other defaults
        if (!empty($validated['is_default'])) {
            Cabang::where('is_default', true)->update(['is_default' => false]);
        }

        Cabang::create($validated);

        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Cabang $cabang)
    {
        return view('cabang.edit', compact('cabang'));
    }

    public function update(Request $request, Cabang $cabang)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'status' => 'required|in:aktif,nonaktif',
            'is_default' => 'nullable|boolean',
        ]);

        // If this is set as default, unset other defaults
        if (!empty($validated['is_default'])) {
            Cabang::where('is_default', true)->where('id', '!=', $cabang->id)->update(['is_default' => false]);
        }

        $cabang->update($validated);

        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Cabang $cabang)
    {
        // Don't allow deleting default cabang
        if ($cabang->is_default) {
            return back()->with('error', 'Cabang default tidak dapat dihapus.');
        }

        $cabang->update(['status' => 'nonaktif']);
        return redirect()->route('cabang.index')
            ->with('success', 'Cabang berhasil dinonaktifkan.');
    }
}
