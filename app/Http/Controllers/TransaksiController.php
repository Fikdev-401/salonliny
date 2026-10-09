<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Konfigurasi;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Services\TransaksiWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function __construct(
        private readonly TransaksiWorkflowService $workflowService
    ) {
    }

    public function index(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'kasir', 'details.layanan', 'details.produk']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('status')) {
            if ($request->status === 'lunas') {
                $query->where('status_pembayaran', 'lunas');
            } elseif ($request->status === 'belum_lunas') {
                $query->where('status_pembayaran', 'pending');
            }
        }

        $totalPemasukan = (clone $query)
            ->where('status_pembayaran', 'lunas')
            ->sum('total');

        $transaksis = $query->orderByDesc('tanggal_transaksi')
            ->paginate(20)
            ->withQueryString();

        return view('transaksi.index', compact('transaksis', 'totalPemasukan'));
    }

    public function create()
    {
        return view('transaksi.create', $this->formData());
    }

    public function store(Request $request)
    {
        try {
            $transaksi = $this->workflowService->create(
                $this->validatePayload($request),
                $request->user()
            );

            return redirect()
                ->route('transaksi.show', $transaksi)
                ->with('success', 'Transaksi berhasil dibuat.');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            Log::error('Gagal membuat transaksi.', [
                'user_id' => Auth::id(),
                'message' => $throwable->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan transaksi. Silakan coba lagi.');
        }
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load([
            'pelanggan',
            'kasir',
            'details.layanan',
            'details.produk.jenisProduk',
            'details.karyawanUtama',
            'details.karyawanPendukung1',
            'details.karyawanPendukung2',
        ]);

        return view('transaksi.show', [
            'transaksi' => $transaksi,
            'whatsappShareUrl' => $this->buildWhatsappShareUrl($transaksi),
        ]);
    }

    public function edit(Transaksi $transaksi)
    {
        $this->ensureSuperAdmin();

        $transaksi->load([
            'pelanggan',
            'details.layanan',
            'details.produk.jenisProduk',
            'details.karyawanUtama',
            'details.karyawanPendukung1',
            'details.karyawanPendukung2',
        ]);

        return view('transaksi.edit', array_merge(
            ['transaksi' => $transaksi],
            $this->formData()
        ));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $this->ensureSuperAdmin();

        try {
            $transaksi = $this->workflowService->update(
                $transaksi,
                $this->validatePayload($request),
                $request->user()
            );

            return redirect()
                ->route('transaksi.show', $transaksi)
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $throwable) {
            Log::error('Gagal memperbarui transaksi.', [
                'transaksi_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'message' => $throwable->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui transaksi. Silakan coba lagi.');
        }
    }

    public function destroy(Transaksi $transaksi)
    {
        $this->ensureSuperAdmin();

        try {
            $this->workflowService->delete($transaksi);

            return redirect()
                ->route('transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus. Stok dan statistik pelanggan sudah disesuaikan.');
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        } catch (\Throwable $throwable) {
            Log::error('Gagal menghapus transaksi.', [
                'transaksi_id' => $transaksi->id,
                'user_id' => Auth::id(),
                'message' => $throwable->getMessage(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus transaksi.');
        }
    }

    public function cetakStruk(Transaksi $transaksi)
    {
        $transaksi->load([
            'pelanggan',
            'kasir',
            'details.layanan',
            'details.produk.jenisProduk',
            'details.karyawanUtama',
            'details.karyawanPendukung1',
            'details.karyawanPendukung2',
        ]);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('transaksi.struk', compact('transaksi'));

        return $pdf->stream('struk-' . $transaksi->nomor_transaksi . '.pdf');
    }

    public function getLayananDetail(int $id)
    {
        $layanan = Layanan::query()
            ->where('status', 'aktif')
            ->findOrFail($id);

        return response()->json($layanan);
    }

    public function getProdukDetail(int $id)
    {
        $produk = Produk::query()
            ->with('jenisProduk')
            ->forTransaksi()
            ->where('status', 'aktif')
            ->findOrFail($id);

        return response()->json($produk);
    }

    private function formData(): array
    {
        return [
            'pelanggans' => Pelanggan::query()
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get(),
            'layanans' => Layanan::query()
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get(),
            'produks' => Produk::query()
                ->with('jenisProduk')
                ->forTransaksi()
                ->where('status', 'aktif')
                ->orderBy('nama')
                ->get(),
            'karyawans' => Karyawan::query()
                ->where('status_kerja', 'aktif')
                ->orderBy('nama')
                ->get(),
        ];
    }

    private function validatePayload(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal_transaksi' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.tipe' => 'required|in:layanan,produk',
            'items.*.item_id' => 'required|integer|min:1',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.karyawan_utama_id' => 'nullable|exists:karyawans,id',
            'items.*.karyawan_pendukung_1_id' => 'nullable|exists:karyawans,id',
            'items.*.karyawan_pendukung_2_id' => 'nullable|exists:karyawans,id',
            'items.*.insentif_utama' => 'nullable|numeric|min:0',
            'items.*.insentif_pendukung_1' => 'nullable|numeric|min:0',
            'items.*.insentif_pendukung_2' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,debit,kredit',
            'catatan' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->input('items', []) as $index => $item) {
                if (($item['tipe'] ?? null) === 'layanan' && empty($item['karyawan_utama_id'])) {
                    $validator->errors()->add("items.{$index}.karyawan_utama_id", 'Karyawan utama wajib diisi untuk item layanan.');
                }
            }
        });

        return $validator->validate();
    }

    private function ensureSuperAdmin(): void
    {
        if (!Auth::user()?->isSuperAdmin()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah transaksi.');
        }
    }

    private function buildWhatsappShareUrl(Transaksi $transaksi): ?string
    {
        $phone = $this->normalizeWhatsappPhone($transaksi->pelanggan?->no_hp);

        if (!$phone) {
            return null;
        }

        $salonName = Konfigurasi::getInstance()->nama_perusahaan ?: config('app.name', 'SalonBarber');
        $message = [
            "{$salonName}",
            "Ringkasan transaksi {$transaksi->nomor_transaksi}",
            'Tanggal: ' . $transaksi->tanggal_transaksi->copy()->timezone(config('app.timezone'))->format('d/m/Y H:i') . ' WITA',
            '',
            'Item:',
        ];

        foreach ($transaksi->details as $detail) {
            $message[] = '- ' . $detail->nama_item . ' x' . $detail->jumlah . ' = ' . rupiah($detail->total);
        }

        $message[] = '';
        $message[] = 'Subtotal: ' . rupiah($transaksi->subtotal);
        $message[] = 'Diskon: ' . rupiah($transaksi->diskon);
        $message[] = 'Total: ' . rupiah($transaksi->total);
        $message[] = 'Bayar: ' . rupiah($transaksi->bayar);
        $message[] = 'Kembalian: ' . rupiah($transaksi->kembalian);
        $message[] = '';
        $message[] = 'Terima kasih telah berkunjung.';

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode(implode("\n", $message));
    }

    private function normalizeWhatsappPhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return '62' . ltrim($digits, '0');
    }
}
