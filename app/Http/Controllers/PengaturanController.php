<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\JenisProduk;
use App\Models\Konfigurasi;

class PengaturanController extends Controller
{
    public function index()
    {
        $konfigurasi = Konfigurasi::getInstance();
        $cabangs = Cabang::orderBy('created_at', 'desc')->get();
        $jenisProduks = JenisProduk::query()->orderBy('id')->get();

        return view('pengaturan.index', compact('konfigurasi', 'cabangs', 'jenisProduks'));
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

        return redirect()->route('pengaturan.index')
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
