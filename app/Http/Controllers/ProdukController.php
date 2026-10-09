<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisProduk;
use App\Models\Produk;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('jenisProduk');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis_produk_id')) {
            $query->where('jenis_produk_id', $request->jenis_produk_id);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('stok_status')) {
            if ($request->stok_status === 'menipis') {
                $query->whereColumn('stok', '<=', 'stok_minimal')
                      ->where('stok', '>', 0);
            } elseif ($request->stok_status === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        $produks = $query->orderBy('nama')->paginate(20)->withQueryString();
        $kategoris = Produk::distinct()->pluck('kategori')->filter();
        $jenisProduks = JenisProduk::query()->orderBy('nama')->get();

        return view('produk.index', compact('produks', 'kategoris', 'jenisProduks'));
    }

    public function create()
    {
        $jenisProduks = JenisProduk::query()
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('produk.create', compact('jenisProduks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_produk_id' => 'required|exists:jenis_produks,id',
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:50|unique:produks',
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_fleksibel' => 'boolean',
        ]);

        $validated['harga_fleksibel'] = $request->boolean('harga_fleksibel');
        $validated['status'] = 'aktif';

        Produk::create($validated);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Produk $produk)
    {
        $produk->load(['jenisProduk', 'pembelians.user']);
        return view('produk.show', compact('produk'));
    }

    public function edit(Produk $produk)
    {
        $jenisProduks = JenisProduk::query()
            ->where(function ($query) use ($produk) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $produk->jenis_produk_id);
            })
            ->orderBy('nama')
            ->get();

        return view('produk.edit', compact('produk', 'jenisProduks'));
    }

    public function update(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'jenis_produk_id' => 'required|exists:jenis_produks,id',
            'nama' => 'required|string|max:255',
            'kode' => 'nullable|string|max:50|unique:produks,kode,' . $produk->id,
            'deskripsi' => 'nullable|string',
            'kategori' => 'nullable|string|max:100',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_fleksibel' => 'boolean',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['harga_fleksibel'] = $request->boolean('harga_fleksibel');

        $produk->update($validated);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        $produk->update(['status' => 'nonaktif']);
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dinonaktifkan.');
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        $produks = Produk::with('jenisProduk')
            ->where('status', 'aktif')
            ->where('stok', '>', 0)
            ->where('nama', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($produks);
    }
}
