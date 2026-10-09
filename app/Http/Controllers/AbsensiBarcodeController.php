<?php

namespace App\Http\Controllers;

use App\Models\AbsensiBarcode;
use App\Models\Karyawan;
use App\Services\AbsensiBarcodeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiBarcodeController extends Controller
{
    public function __construct(
        private readonly AbsensiBarcodeService $barcodeService
    ) {
    }

    /**
     * Display list of barcodes
     */
    public function index(Request $request)
    {
        $query = AbsensiBarcode::with(['karyawan', 'createdBy']);

        // Filter by date
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            $query->whereDate('tanggal', Carbon::today());
        }

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Filter by tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'used') {
                $query->where('is_used', true);
            } elseif ($request->status === 'active') {
                $query->where('is_used', false)
                      ->where('expires_at', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where('is_used', false)
                      ->where('expires_at', '<=', now());
            }
        }

        $barcodes = $query->orderBy('created_at', 'desc')
                         ->paginate(20)
                         ->withQueryString();

        $karyawans = Karyawan::where('status_kerja', 'aktif')->get();
        $statistics = $this->barcodeService->getStatistics(
            $request->filled('tanggal') ? Carbon::parse($request->tanggal) : null
        );

        return view('absensi.barcodes.index', compact('barcodes', 'karyawans', 'statistics'));
    }

    /**
     * Generate barcode for specific karyawan
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'tipe' => 'required|in:checkin,checkout,both',
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);
        $createdBy = auth()->id();

        try {
            if ($validated['tipe'] === 'both') {
                // Generate both checkin and checkout
                $barcodeIn = $this->barcodeService->generateForKaryawan(
                    $validated['karyawan_id'],
                    $tanggal,
                    'checkin',
                    $createdBy
                );
                $barcodeOut = $this->barcodeService->generateForKaryawan(
                    $validated['karyawan_id'],
                    $tanggal,
                    'checkout',
                    $createdBy
                );
                
                return redirect()->route('absensi.barcodes.index')
                    ->with('success', "Barcode berhasil dibuat. Check-in: {$barcodeIn->barcode}, Check-out: {$barcodeOut->barcode}");
            } else {
                $barcode = $this->barcodeService->generateForKaryawan(
                    $validated['karyawan_id'],
                    $tanggal,
                    $validated['tipe'],
                    $createdBy
                );
                
                return redirect()->route('absensi.barcodes.index')
                    ->with('success', "Barcode {$validated['tipe']} berhasil dibuat: {$barcode->barcode}");
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat barcode: ' . $e->getMessage());
        }
    }

    /**
     * Generate barcodes in bulk
     */
    public function bulkGenerate(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'karyawan_ids' => 'nullable|array',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'generate_for_all' => 'nullable|boolean',
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);
        $createdBy = auth()->id();

        try {
            if (!empty($validated['generate_for_all'])) {
                $barcodes = $this->barcodeService->generateForAllActiveKaryawan($tanggal, $createdBy);
                $count = $barcodes->count();
                
                return redirect()->route('absensi.barcodes.index')
                    ->with('success', "Berhasil membuat {$count} barcode untuk semua karyawan aktif.");
            } elseif (!empty($validated['karyawan_ids'])) {
                $barcodes = $this->barcodeService->generateBulk(
                    $validated['karyawan_ids'],
                    $tanggal,
                    $createdBy
                );
                $count = $barcodes->count();
                
                return redirect()->route('absensi.barcodes.index')
                    ->with('success', "Berhasil membuat {$count} barcode untuk karyawan yang dipilih.");
            } else {
                return redirect()->back()
                    ->with('error', 'Pilih karyawan atau centang "Generate untuk semua karyawan".');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat barcode: ' . $e->getMessage());
        }
    }

    /**
     * Print barcode
     */
    public function print(Request $request)
    {
        $validated = $request->validate([
            'barcode_ids' => 'required|array',
            'barcode_ids.*' => 'exists:absensi_barcodes,id',
        ]);

        $barcodes = AbsensiBarcode::with('karyawan')
            ->whereIn('id', $validated['barcode_ids'])
            ->get();

        return view('absensi.barcodes.print', compact('barcodes'));
    }

    /**
     * Print single barcode
     */
    public function printSingle(AbsensiBarcode $barcode)
    {
        $barcodes = collect([$barcode->load('karyawan')]);
        
        return view('absensi.barcodes.print', compact('barcodes'));
    }

    /**
     * Delete barcode
     */
    public function destroy(AbsensiBarcode $barcode)
    {
        // Cek apakah barcode sudah digunakan
        if ($barcode->is_used) {
            return redirect()->back()
                ->with('error', 'Barcode sudah digunakan dan tidak bisa dihapus.');
        }

        $barcode->delete();

        return redirect()->route('absensi.barcodes.index')
            ->with('success', 'Barcode berhasil dihapus.');
    }

    /**
     * Cleanup expired barcodes
     */
    public function cleanup()
    {
        $count = $this->barcodeService->cleanupExpiredBarcodes();

        return redirect()->route('absensi.barcodes.index')
            ->with('success', "Berhasil membersihkan {$count} barcode expired.");
    }

    /**
     * Get barcode info via AJAX
     */
    public function show(AbsensiBarcode $barcode)
    {
        return response()->json([
            'id' => $barcode->id,
            'barcode' => $barcode->barcode,
            'karyawan' => $barcode->karyawan->nama,
            'tanggal' => $barcode->tanggal->format('d/m/Y'),
            'tipe' => $barcode->tipe,
            'is_used' => $barcode->is_used,
            'is_valid' => $barcode->isValid(),
            'status_label' => $barcode->status_label,
        ]);
    }
}
