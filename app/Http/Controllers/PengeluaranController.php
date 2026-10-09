<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluaran::with('user');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('sumber')) {
            if ($request->sumber === 'manual') {
                $query->where('is_system_generated', false);
            } elseif ($request->sumber === 'system') {
                $query->where('is_system_generated', true);
            }
        }

        $totalPengeluaran = (clone $query)->sum('jumlah');
        $pengeluarans = $query->orderByDesc('tanggal')
            ->paginate(20)
            ->withQueryString();
        $kategoris = Pengeluaran::query()->distinct()->orderBy('kategori')->pluck('kategori')->filter();

        return view('pengeluaran.index', compact('pengeluarans', 'kategoris', 'totalPengeluaran'));
    }

    public function create()
    {
        $kategoris = ['Listrik', 'Air', 'Internet', 'Sewa', 'Alat Kerja', 'Kebersihan', 'Bahan', 'Lainnya'];

        return view('pengeluaran.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $validated['nomor_pengeluaran'] = 'PGL-' . Carbon::parse($validated['tanggal'])->format('Ymd') . '-' . strtoupper(uniqid());
        $validated['user_id'] = Auth::id();
        $validated['sumber_tipe'] = null;
        $validated['sumber_id'] = null;
        $validated['is_system_generated'] = false;

        if ($request->hasFile('bukti_nota')) {
            $validated['bukti_nota'] = $this->storeBukti($request);
        }

        Pengeluaran::create($validated);

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran manual berhasil dicatat.');
    }

    public function show(Pengeluaran $pengeluaran)
    {
        $pengeluaran->load('user');

        return view('pengeluaran.show', [
            'pengeluaran' => $pengeluaran,
            'sourceRoute' => $this->resolveSourceRoute($pengeluaran),
        ]);
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        $this->ensureEditable($pengeluaran);

        $kategoris = ['Listrik', 'Air', 'Internet', 'Sewa', 'Alat Kerja', 'Kebersihan', 'Bahan', 'Lainnya'];

        return view('pengeluaran.edit', compact('pengeluaran', 'kategoris'));
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $this->ensureEditable($pengeluaran);

        $validated = $this->validatePayload($request);

        if ($request->hasFile('bukti_nota')) {
            $validated['bukti_nota'] = $this->storeBukti($request);
        }

        $pengeluaran->update($validated);

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $this->ensureEditable($pengeluaran);

        $pengeluaran->delete();

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    public function downloadBukti(Pengeluaran $pengeluaran)
    {
        if (!$pengeluaran->bukti_nota) {
            abort(404, 'Bukti nota tidak ditemukan.');
        }

        $path = storage_path('app/public/bukti_pengeluaran/' . $pengeluaran->bukti_nota);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($path);
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'nama_pengeluaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'bukti_nota' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);
    }

    private function ensureEditable(Pengeluaran $pengeluaran): void
    {
        if (!Auth::user()?->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pengeluaran.');
        }

        if ($pengeluaran->is_system_generated) {
            abort(403, 'Pengeluaran otomatis hanya bisa diubah dari data sumbernya.');
        }
    }

    private function storeBukti(Request $request): string
    {
        $file = $request->file('bukti_nota');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('bukti_pengeluaran', $filename, 'public');

        return $filename;
    }

    private function resolveSourceRoute(Pengeluaran $pengeluaran): ?string
    {
        if (!$pengeluaran->is_system_generated) {
            return null;
        }

        return match ($pengeluaran->sumber_tipe) {
            Pembelian::class => route('pembelian.show', $pengeluaran->sumber_id),
            Gaji::class => route('gaji.show', $pengeluaran->sumber_id),
            default => null,
        };
    }
}
