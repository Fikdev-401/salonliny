<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventaris::with(['creator', 'cabang']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($builder) use ($search) {
                $builder->where('kode_aset', 'like', "%{$search}%")
                    ->orWhere('nama_aset', 'like', "%{$search}%")
                    ->orWhere('jenis_aset', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inventaris = $query->orderByDesc('tanggal_input')
            ->paginate(20)
            ->withQueryString();

        return view('inventaris.index', compact('inventaris'));
    }

    public function create()
    {
        return view('inventaris.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        Inventaris::create([
            'kode_aset' => $validated['kode_aset'] ?: $this->generateKodeAset($validated['tanggal_input']),
            'nama_aset' => $validated['nama_aset'],
            'jenis_aset' => $validated['jenis_aset'],
            'jumlah' => $validated['jumlah'],
            'satuan' => $validated['satuan'],
            'tanggal_input' => $validated['tanggal_input'],
            'status' => $validated['status'],
            'keterangan' => $validated['keterangan'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('inventaris.index')
            ->with('success', 'Inventaris berhasil ditambahkan.');
    }

    public function show(Inventaris $inventaris)
    {
        $inventaris->load(['creator', 'cabang', 'pembelians.user']);

        return view('inventaris.show', compact('inventaris'));
    }

    public function edit(Inventaris $inventaris)
    {
        return view('inventaris.edit', compact('inventaris'));
    }

    public function update(Request $request, Inventaris $inventaris)
    {
        $validated = $this->validatePayload($request, $inventaris->id);

        $inventaris->update($validated);

        return redirect()->route('inventaris.index')
            ->with('success', 'Inventaris berhasil diperbarui.');
    }

    public function destroy(Inventaris $inventaris)
    {
        if ($inventaris->pembelians()->exists()) {
            return back()->with('error', 'Inventaris yang berasal dari pembelian tidak bisa dihapus langsung. Ubah atau hapus dari data pembelian sumbernya.');
        }

        $inventaris->delete();

        return redirect()->route('inventaris.index')
            ->with('success', 'Inventaris berhasil dihapus.');
    }

    private function validatePayload(Request $request, ?int $inventarisId = null): array
    {
        return $request->validate([
            'kode_aset' => 'nullable|string|max:50|unique:inventaris,kode_aset,' . $inventarisId,
            'nama_aset' => 'required|string|max:255',
            'jenis_aset' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'tanggal_input' => 'required|date',
            'status' => 'required|in:bagus,rusak',
            'keterangan' => 'nullable|string',
        ]);
    }

    private function generateKodeAset(string $tanggal): string
    {
        return 'AST-' . Carbon::parse($tanggal)->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
