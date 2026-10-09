<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Produk;
use App\Services\PembelianWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PembelianController extends Controller
{
    public function __construct(
        private readonly PembelianWorkflowService $workflowService
    ) {
    }

    public function index(Request $request)
    {
        $query = Pembelian::with(['produk.jenisProduk', 'inventaris', 'user', 'pengeluaran']);
        $this->applyActorCabangScope($query, $request->user());

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('jenis_pembelian')) {
            $query->where('jenis_pembelian', $request->jenis_pembelian);
        }

        $totalPembelian = (clone $query)->sum('total');
        $pembelians = $query->orderByDesc('tanggal')
            ->paginate(20)
            ->withQueryString();

        return view('pembelian.index', compact('pembelians', 'totalPembelian'));
    }

    public function create()
    {
        return view('pembelian.create', $this->formData());
    }

    public function store(Request $request)
    {
        try {
            $this->workflowService->create($this->validatePayload($request), $request->user());

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Pembelian berhasil dicatat dan otomatis masuk ke pengeluaran.');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            Log::error('Gagal menyimpan pembelian.', [
                'user_id' => Auth::id(),
                'message' => $throwable->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pembelian.');
        }
    }

    public function show(Pembelian $pembelian)
    {
        $this->ensureActorCanAccess($pembelian, request()->user());

        $pembelian->load(['produk.jenisProduk', 'inventaris', 'user', 'pengeluaran']);

        return view('pembelian.show', compact('pembelian'));
    }

    public function edit(Pembelian $pembelian)
    {
        $this->ensureActorCanAccess($pembelian, request()->user());

        return view('pembelian.edit', array_merge(
            ['pembelian' => $pembelian->load(['produk.jenisProduk', 'inventaris', 'pengeluaran'])],
            $this->formData()
        ));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $this->ensureActorCanAccess($pembelian, $request->user());

        try {
            $this->workflowService->update($pembelian, $this->validatePayload($request, $pembelian), $request->user());

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Pembelian berhasil diperbarui dan pengeluaran terkait sudah disinkronkan.');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            Log::error('Gagal memperbarui pembelian.', [
                'pembelian_id' => $pembelian->id,
                'user_id' => Auth::id(),
                'message' => $throwable->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pembelian.');
        }
    }

    public function destroy(Pembelian $pembelian)
    {
        $this->ensureActorCanAccess($pembelian, request()->user());

        try {
            $this->workflowService->delete($pembelian, request()->user());

            return redirect()
                ->route('pembelian.index')
                ->with('success', 'Pembelian berhasil dihapus.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        } catch (\Throwable $throwable) {
            Log::error('Gagal menghapus pembelian.', [
                'pembelian_id' => $pembelian->id,
                'user_id' => Auth::id(),
                'message' => $throwable->getMessage(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus pembelian.');
        }
    }

    private function formData(): array
    {
        return [
            'produks' => Produk::with('jenisProduk')
                ->where('status', 'aktif')
                ->whereHas('jenisProduk', function ($query) {
                    $query->where('status', 'aktif');
                })
                ->orderBy('nama')
                ->get(),
            'jenisPembelians' => [
                'produk_jual' => 'Produk untuk Dijual',
                'bahan_pakai' => 'Bahan Dipakai',
                'alat_inventaris' => 'Alat Inventaris',
                'barang_sewa' => 'Barang yang Disewakan',
            ],
            'statusInventaris' => [
                'bagus' => 'Bagus',
                'rusak' => 'Rusak',
            ],
        ];
    }

    private function applyActorCabangScope($query, $actor): void
    {
        if (!$actor || $actor->isSuperAdmin()) {
            return;
        }

        $query->whereHas('user', function ($userQuery) use ($actor) {
            if ($actor->cabang_id) {
                $userQuery->where('cabang_id', $actor->cabang_id);
                return;
            }

            $userQuery->whereNull('cabang_id');
        });
    }

    private function ensureActorCanAccess(Pembelian $pembelian, $actor): void
    {
        if (!$actor || $actor->isSuperAdmin()) {
            return;
        }

        $pembelian->loadMissing('user');
        $recordCabangId = $pembelian->user?->cabang_id;

        if ((int) $recordCabangId !== (int) $actor->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke pembelian cabang lain.');
        }
    }

    private function validatePayload(Request $request, ?Pembelian $pembelian = null): array
    {
        $inventarisId = $pembelian?->inventaris_id;

        return $request->validate([
            'tanggal' => 'required|date',
            'jenis_pembelian' => ['required', Rule::in(['produk_jual', 'bahan_pakai', 'alat_inventaris', 'barang_sewa'])],
            'produk_id' => [
                Rule::requiredIf(fn () => $request->input('jenis_pembelian') !== 'alat_inventaris'),
                'nullable',
                'exists:produks,id',
            ],
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'no_hp_supplier' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string',
            'kode_aset' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('inventaris', 'kode_aset')->ignore($inventarisId),
            ],
            'nama_aset' => [Rule::requiredIf(fn () => $request->input('jenis_pembelian') === 'alat_inventaris'), 'nullable', 'string', 'max:255'],
            'jenis_aset' => [Rule::requiredIf(fn () => $request->input('jenis_pembelian') === 'alat_inventaris'), 'nullable', 'string', 'max:100'],
            'satuan_aset' => [Rule::requiredIf(fn () => $request->input('jenis_pembelian') === 'alat_inventaris'), 'nullable', 'string', 'max:50'],
            'status_aset' => [Rule::requiredIf(fn () => $request->input('jenis_pembelian') === 'alat_inventaris'), 'nullable', Rule::in(['bagus', 'rusak'])],
            'keterangan_aset' => 'nullable|string',
        ]);
    }
}
