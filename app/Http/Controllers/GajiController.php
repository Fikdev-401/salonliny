<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;
use App\Models\TransaksiDetail;
use App\Services\GajiSnapshotService;
use App\Services\PengeluaranSyncService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GajiController extends Controller
{
    public function __construct(
        private readonly GajiSnapshotService $gajiSnapshotService,
        private readonly PengeluaranSyncService $pengeluaranSyncService
    ) {
    }

    public function index(Request $request)
    {
        $query = Gaji::with(['karyawan', 'diajukanOleh', 'disetujuiOleh', 'pengeluaran']);

        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        } else {
            $query->where('periode', now()->format('Y-m'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $gajis = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('gaji.index', compact('gajis'));
    }

    public function create()
    {
        $this->requireRole(['admin', 'super-admin']);

        $karyawans = Karyawan::query()
            ->with(['riwayatGajis' => fn ($query) => $query->limit(1)])
            ->where('status_kerja', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('gaji.create', compact('karyawans'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'periode' => 'required|date_format:Y-m',
        ]);

        try {
            $karyawan = Karyawan::findOrFail($validated['karyawan_id']);
            $snapshot = $this->gajiSnapshotService->buildSnapshot($karyawan, $validated['periode']);

            return response()->json([
                'gaji_pokok' => (float) $snapshot['gaji_pokok'],
                'total_insentif' => (float) $snapshot['total_insentif'],
                'jumlah_hadir' => (int) $snapshot['jumlah_hadir'],
                'jumlah_izin' => (int) $snapshot['jumlah_izin'],
                'jumlah_sakit' => (int) $snapshot['jumlah_sakit'],
                'jumlah_alpha' => (int) $snapshot['jumlah_alpha'],
                'base_total' => (float) $snapshot['base_total'],
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function store(Request $request)
    {
        $this->requireRole(['admin', 'super-admin']);

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'periode' => 'required|date_format:Y-m',
            'bonus' => 'nullable|numeric|min:0',
            'thr' => 'nullable|numeric|min:0',
            'potongan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if (Gaji::where('karyawan_id', $validated['karyawan_id'])->where('periode', $validated['periode'])->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Gaji untuk karyawan ini pada periode tersebut sudah ada.');
        }

        try {
            $karyawan = Karyawan::findOrFail($validated['karyawan_id']);
            $snapshot = $this->gajiSnapshotService->buildSnapshot($karyawan, $validated['periode'], $validated);

            Gaji::create([
                'karyawan_id' => $karyawan->id,
                'periode' => $validated['periode'],
                'tanggal_mulai' => $snapshot['tanggal_mulai'],
                'tanggal_selesai' => $snapshot['tanggal_selesai'],
                'gaji_pokok' => $snapshot['gaji_pokok'],
                'total_insentif' => $snapshot['total_insentif'],
                'bonus' => $snapshot['bonus'],
                'thr' => $snapshot['thr'],
                'potongan' => $snapshot['potongan'],
                'total_gaji' => $snapshot['total_gaji'],
                'jumlah_hadir' => $snapshot['jumlah_hadir'],
                'jumlah_izin' => $snapshot['jumlah_izin'],
                'jumlah_sakit' => $snapshot['jumlah_sakit'],
                'jumlah_alpha' => $snapshot['jumlah_alpha'],
                'status' => 'draft',
                'diajukan_oleh' => auth()->id(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            return redirect()->route('gaji.index')
                ->with('success', 'Gaji berhasil dihitung dari snapshot historis karyawan.');
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }
    }

    public function show(Gaji $gaji)
    {
        $gaji->load(['karyawan', 'diajukanOleh', 'disetujuiOleh', 'pengeluaran']);

        $transaksiDetails = TransaksiDetail::with(['transaksi.pelanggan', 'layanan'])
            ->whereHas('transaksi', function ($query) use ($gaji) {
                $query->whereNull('deleted_at')
                    ->whereBetween('tanggal_transaksi', [$gaji->tanggal_mulai->startOfDay(), $gaji->tanggal_selesai->endOfDay()])
                    ->where('status', '!=', 'batal');
            })
            ->where(function ($query) use ($gaji) {
                $query->where('karyawan_utama_id', $gaji->karyawan_id)
                    ->orWhere('karyawan_pendukung_1_id', $gaji->karyawan_id)
                    ->orWhere('karyawan_pendukung_2_id', $gaji->karyawan_id);
            })
            ->get();

        return view('gaji.show', compact('gaji', 'transaksiDetails'));
    }

    public function edit(Gaji $gaji)
    {
        $this->requireRole(['admin', 'super-admin']);
        $this->ensureEditable($gaji);

        return view('gaji.edit', compact('gaji'));
    }

    public function update(Request $request, Gaji $gaji)
    {
        $this->requireRole(['admin', 'super-admin']);
        $this->ensureEditable($gaji);

        $validated = $request->validate([
            'bonus' => 'nullable|numeric|min:0',
            'thr' => 'nullable|numeric|min:0',
            'potongan' => 'nullable|numeric|min:0',
            'keterangan' => [$gaji->status === 'dibayar' ? 'required' : 'nullable', 'string'],
        ]);

        $bonus = (float) ($validated['bonus'] ?? 0);
        $thr = (float) ($validated['thr'] ?? 0);
        $potongan = (float) ($validated['potongan'] ?? 0);

        $gaji->update([
            'bonus' => $bonus,
            'thr' => $thr,
            'potongan' => $potongan,
            'total_gaji' => (float) $gaji->gaji_pokok + (float) $gaji->total_insentif + $bonus + $thr - $potongan,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        if ($gaji->status === 'dibayar') {
            $this->pengeluaranSyncService->syncForGaji($gaji->fresh(['karyawan']));
        }

        return redirect()->route('gaji.index')
            ->with('success', 'Data gaji berhasil diperbarui tanpa mengubah snapshot periode.');
    }

    public function approve(Gaji $gaji)
    {
        $this->requireRole(['pimpinan', 'super-admin']);
        $this->ensureStatus($gaji, 'draft', 'Gaji hanya dapat disetujui dari status draft.');

        $gaji->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'disetujui_at' => now(),
        ]);

        return redirect()->route('gaji.index')
            ->with('success', 'Gaji berhasil disetujui.');
    }

    public function bayar(Request $request, Gaji $gaji)
    {
        $this->requireRole(['admin', 'super-admin']);
        $this->ensureStatus($gaji, 'disetujui', 'Gaji hanya dapat dibayar setelah disetujui.');

        $request->validate([
            'tanggal_bayar' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $gaji) {
            $gaji->update([
                'status' => 'dibayar',
                'tanggal_bayar' => $request->tanggal_bayar,
            ]);

            $this->pengeluaranSyncService->syncForGaji($gaji->fresh(['karyawan']));
        });

        return redirect()->route('gaji.index')
            ->with('success', 'Gaji berhasil dibayarkan dan otomatis tercatat sebagai pengeluaran.');
    }

    public function destroy(Request $request, Gaji $gaji)
    {
        $this->requireRole(['admin', 'super-admin']);

        if ($gaji->status === 'dibayar') {
            $this->requireRole(['super-admin']);
            $request->validate([
                'alasan' => 'required|string|min:10',
            ]);
        } elseif ($gaji->status !== 'draft') {
            abort(422, 'Gaji yang sudah disetujui tidak dapat dihapus sebelum dibatalkan oleh super-admin.');
        }

        DB::transaction(function () use ($gaji) {
            $this->pengeluaranSyncService->deleteForGaji($gaji);
            $gaji->delete();
        });

        return redirect()->route('gaji.index')
            ->with('success', 'Data gaji berhasil dihapus.');
    }

    public function slipGaji(Gaji $gaji)
    {
        $gaji->load(['karyawan']);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('gaji.slip', compact('gaji'));

        return $pdf->stream('slip-gaji-' . $gaji->karyawan->nama . '-' . $gaji->periode . '.pdf');
    }

    private function requireRole(array $roles): void
    {
        if (!request()->user()?->hasRole($roles)) {
            abort(403, 'Anda tidak memiliki akses untuk aksi gaji ini.');
        }
    }

    private function ensureStatus(Gaji $gaji, string $status, string $message): void
    {
        if ($gaji->status !== $status) {
            abort(422, $message);
        }
    }

    private function ensureEditable(Gaji $gaji): void
    {
        if ($gaji->status === 'draft') {
            return;
        }

        if ($gaji->status === 'dibayar' && request()->user()?->isSuperAdmin()) {
            return;
        }

        abort(422, 'Gaji hanya dapat diedit saat masih draft.');
    }
}
