<?php

namespace App\Http\Controllers;

use App\Models\JenisProduk;
use Illuminate\Http\Request;

class JenisProdukController extends Controller
{
    public function update(Request $request, JenisProduk $jenisProduk)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $jenisProduk->update($validated);

        return redirect()->route('pengaturan.index')
            ->with('success', 'Master jenis produk berhasil diperbarui.');
    }
}
