<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Konfigurasi;

class KonfigurasiController extends Controller
{
    public function index()
    {
        $konfigurasi = Konfigurasi::getInstance();
        return view('konfigurasi.index', compact('konfigurasi'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'jam_buka' => 'nullable|string|max:10',
            'jam_tutup' => 'nullable|string|max:10',
            'pajak_persen' => 'nullable|numeric|min:0|max:100',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $konfigurasi = Konfigurasi::getInstance();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo.' . $file->getClientOriginalExtension();
            $file->storeAs('konfigurasi', $filename, 'public');
            $validated['logo'] = $filename;
        }

        $konfigurasi->update($validated);

        return redirect()->route('konfigurasi.index')
            ->with('success', 'Konfigurasi berhasil diperbarui.');
    }
}
