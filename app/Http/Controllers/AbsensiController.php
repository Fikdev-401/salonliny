<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\AbsensiBarcode;
use App\Models\Karyawan;
use App\Services\AbsensiBarcodeService;
use App\Services\AbsensiPhotoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DomainException;

class AbsensiController extends Controller
{
    public function __construct(
        private readonly AbsensiBarcodeService $barcodeService,
        private readonly AbsensiPhotoService $photoService
    ) {
    }

    /**
     * Display absensi list
     */
    public function index(Request $request)
    {
        $query = Absensi::with(['karyawan', 'diverifikasiOleh']);

        $selectedDate = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            $query->whereDate('tanggal', Carbon::today());
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'pending_verifikasi' => (clone $query)->where('status_verifikasi', 'pending')->count(),
            'terverifikasi' => (clone $query)->where('status_verifikasi', 'terverifikasi')->count(),
            'sudah_checkout' => (clone $query)->whereNotNull('jam_keluar')->count(),
            'dengan_foto' => (clone $query)
                ->where(function ($builder) {
                    $builder->whereNotNull('foto_masuk')
                        ->orWhereNotNull('foto_keluar');
                })
                ->count(),
        ];

        $absensis = $query->orderBy('jam_masuk', 'desc')->paginate(20)->withQueryString();
        $karyawans = Karyawan::where('status_kerja', 'aktif')->get();

        return view('absensi.index', compact('absensis', 'karyawans', 'stats', 'selectedDate'));
    }

    /**
     * Display rekap absensi
     */
    public function rekap(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($bulan . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($bulan . '-01')->endOfMonth();

        $karyawans = Karyawan::where('status_kerja', 'aktif')->get();
        
        $rekap = [];
        foreach ($karyawans as $karyawan) {
            $absensiBulan = Absensi::where('karyawan_id', $karyawan->id)
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->get();

            $rekap[] = [
                'karyawan' => $karyawan,
                'hadir' => $absensiBulan->where('status', 'hadir')->count(),
                'izin' => $absensiBulan->where('status', 'izin')->count(),
                'sakit' => $absensiBulan->where('status', 'sakit')->count(),
                'alpha' => $absensiBulan->where('status', 'alpha')->count(),
                'cuti' => $absensiBulan->where('status', 'cuti')->count(),
                'total' => $absensiBulan->count(),
            ];
        }

        return view('absensi.rekap', compact('rekap', 'bulan'));
    }

    /**
     * Show scan barcode page
     */
    public function scan()
    {
        $karyawan = $this->currentKaryawan();
        $todayAttendance = null;
        $barcodeIn = null;
        $barcodeOut = null;
        
        if ($karyawan instanceof Karyawan) {
            // Check today's attendance
            $todayAttendance = Absensi::where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', Carbon::today())
                ->first();

            // Get today's barcodes
            $barcodeIn = $this->barcodeService->getTodayBarcode($karyawan->id, 'checkin');
            $barcodeOut = $this->barcodeService->getTodayBarcode($karyawan->id, 'checkout');
        }

        return view('absensi.scan', compact('karyawan', 'todayAttendance', 'barcodeIn', 'barcodeOut'));
    }

    /**
     * Process scanned barcode
     */
    public function processScan(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string',
            'foto' => ['required', 'string', 'regex:/^data:image\/(jpeg|jpg|png|webp);base64,/i'],
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $karyawan = $this->currentKaryawan();
        
        if (!$karyawan instanceof Karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
            ], 403);
        }

        // Validate barcode
        $validation = $this->barcodeService->validateBarcode($validated['barcode']);
        
        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ], 400);
        }

        $barcodeRecord = $validation['barcode'];

        // Cek apakah barcode milik karyawan yang login
        if ($barcodeRecord->karyawan_id !== $karyawan->id) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode ini bukan untuk akun Anda.',
            ], 403);
        }

        $photoPath = null;

        try {
            DB::transaction(function () use ($request, $validated, $karyawan, $barcodeRecord, &$photoPath) {
                $barcodeRecord = AbsensiBarcode::query()
                    ->whereKey($barcodeRecord->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$barcodeRecord->isValid()) {
                    throw new DomainException('Barcode sudah digunakan atau sudah kadaluarsa.');
                }

                if ($barcodeRecord->karyawan_id !== $karyawan->id) {
                    throw new DomainException('Barcode ini bukan untuk akun Anda.');
                }

                $locationMissing = empty($validated['latitude']) || empty($validated['longitude']);
                $verificationNote = $locationMissing
                    ? 'Lokasi GPS tidak tersedia saat ' . ($barcodeRecord->tipe === 'checkin' ? 'check-in' : 'check-out') . '.'
                    : null;

                // Save photo
                $photoPath = $this->photoService->saveBase64Photo(
                    $validated['foto'],
                    $barcodeRecord->tipe,
                    $karyawan->id,
                    Carbon::today()
                );

                // Add watermark
                $watermarkText = $karyawan->nama . ' | ' . now()->format('d/m/Y H:i:s');
                $this->photoService->addWatermark($photoPath, $watermarkText);

                if ($barcodeRecord->tipe === 'checkin') {
                    // Create check-in record
                    $existing = Absensi::where('karyawan_id', $karyawan->id)
                        ->whereDate('tanggal', Carbon::today())
                        ->first();

                    if ($existing) {
                        throw new DomainException('Anda sudah check-in hari ini.');
                    }

                    Absensi::create([
                        'karyawan_id' => $karyawan->id,
                        'tanggal' => Carbon::today(),
                        'jam_masuk' => now()->format('H:i'),
                        'status' => 'hadir',
                        'barcode' => $barcodeRecord->barcode,
                        'foto_masuk' => $photoPath,
                        'latitude_masuk' => $validated['latitude'] ?? null,
                        'longitude_masuk' => $validated['longitude'] ?? null,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'status_verifikasi' => 'pending',
                        'lokasi_missing' => $locationMissing,
                        'catatan_verifikasi' => $verificationNote,
                    ]);
                } else {
                    // Update check-out record
                    $absensi = Absensi::where('karyawan_id', $karyawan->id)
                        ->whereDate('tanggal', Carbon::today())
                        ->first();

                    if (!$absensi) {
                        throw new DomainException('Anda belum check-in hari ini.');
                    }

                    if ($absensi->jam_keluar) {
                        throw new DomainException('Anda sudah check-out hari ini.');
                    }

                    $absensi->update([
                        'jam_keluar' => now()->format('H:i'),
                        'foto_keluar' => $photoPath,
                        'latitude_keluar' => $validated['latitude'] ?? null,
                        'longitude_keluar' => $validated['longitude'] ?? null,
                        'lokasi_missing' => $absensi->lokasi_missing || $locationMissing,
                        'status_verifikasi' => 'pending',
                        'catatan_verifikasi' => trim(implode("\n", array_filter([
                            $absensi->catatan_verifikasi,
                            $verificationNote,
                        ]))),
                    ]);
                }

                // Mark barcode as used
                $barcodeRecord->markAsUsed();
            });

            $message = $barcodeRecord->tipe === 'checkin' 
                ? 'Check-in berhasil dicatat.' 
                : 'Check-out berhasil dicatat.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'tipe' => $barcodeRecord->tipe,
            ]);

        } catch (DomainException $e) {
            if ($photoPath) {
                $this->photoService->deletePhoto($photoPath);
            }

            Log::warning('Proses scan barcode ditolak.', [
                'karyawan_id' => $karyawan->id,
                'barcode' => $validated['barcode'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            if ($photoPath) {
                $this->photoService->deletePhoto($photoPath);
            }

            Log::error('Gagal proses scan barcode', [
                'karyawan_id' => $karyawan->id,
                'barcode' => $validated['barcode'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * My attendance history
     */
    public function myAttendance(Request $request)
    {
        $karyawan = $this->currentKaryawan();
        
        if (!$karyawan instanceof Karyawan) {
            return redirect()->route('dashboard')
                ->with('error', 'Data karyawan tidak ditemukan.');
        }

        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($bulan . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($bulan . '-01')->endOfMonth();

        $absensis = Absensi::where('karyawan_id', $karyawan->id)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'hadir' => $absensis->where('status', 'hadir')->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alpha' => $absensis->where('status', 'alpha')->count(),
            'cuti' => $absensis->where('status', 'cuti')->count(),
            'total' => $absensis->count(),
        ];

        return view('absensi.my-attendance', compact('karyawan', 'absensis', 'stats', 'bulan'));
    }

    /**
     * Get today's barcode for current user
     */
    public function getTodayBarcode()
    {
        $karyawan = $this->currentKaryawan();
        
        if (!$karyawan instanceof Karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.',
            ], 404);
        }

        $barcodeIn = $this->barcodeService->getTodayBarcode($karyawan->id, 'checkin');
        $barcodeOut = $this->barcodeService->getTodayBarcode($karyawan->id, 'checkout');

        return response()->json([
            'success' => true,
            'barcode_in' => $barcodeIn ? [
                'barcode' => $barcodeIn->barcode,
                'is_used' => $barcodeIn->is_used,
                'is_valid' => $barcodeIn->isValid(),
            ] : null,
            'barcode_out' => $barcodeOut ? [
                'barcode' => $barcodeOut->barcode,
                'is_used' => $barcodeOut->is_used,
                'is_valid' => $barcodeOut->isValid(),
            ] : null,
        ]);
    }

    /**
     * Monitoring dashboard for admin
     */
    public function monitoring(Request $request)
    {
        $tanggal = $request->filled('tanggal') 
            ? Carbon::parse($request->tanggal) 
            : Carbon::today();

        // Get all active karyawan
        $karyawans = Karyawan::where('status_kerja', 'aktif')->get();

        // Get today's attendance
        $absensis = Absensi::with('karyawan')
            ->whereDate('tanggal', $tanggal)
            ->get();

        // Calculate statistics
        $stats = [
            'total_karyawan' => $karyawans->count(),
            'sudah_hadir' => $absensis->where('status', 'hadir')->whereNotNull('jam_masuk')->count(),
            'belum_hadir' => $karyawans->count() - $absensis->where('status', 'hadir')->count(),
            'sudah_pulang' => $absensis->whereNotNull('jam_keluar')->count(),
            'terlambat' => $absensis->filter(function($a) {
                return $a->jam_masuk && Carbon::parse($a->jam_masuk)->format('H:i') > '08:00';
            })->count(),
        ];

        // Detail list
        $hadirList = $absensis->where('status', 'hadir')->sortByDesc('jam_masuk');
        $belumHadirList = $karyawans->whereNotIn('id', $absensis->pluck('karyawan_id'));

        // Recent attendance (last 10)
        $recentAbsensis = $absensis->sortByDesc('jam_masuk')->take(10);

        return view('absensi.monitoring', compact(
            'tanggal',
            'stats',
            'hadirList',
            'belumHadirList',
            'recentAbsensis'
        ));
    }

    /**
     * Show absensi detail
     */
    public function show(Absensi $absensi)
    {
        $absensi->load(['karyawan', 'diverifikasiOleh']);
        return view('absensi.detail', compact('absensi'));
    }

    /**
     * Verify absensi photo
     */
    public function verify(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'status_verifikasi' => 'required|in:terverifikasi,ditolak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $absensi->update([
            'status_verifikasi' => $validated['status_verifikasi'],
            'catatan_verifikasi' => $validated['catatan_verifikasi'],
            'diverifikasi_oleh' => auth()->id(),
            'diverifikasi_at' => now(),
        ]);

        $message = $validated['status_verifikasi'] === 'terverifikasi' 
            ? 'Absensi berhasil diverifikasi.' 
            : 'Absensi ditolak.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Store absensi (manual by admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable',
            'jam_keluar' => 'nullable',
            'status' => 'required|in:hadir,izin,sakit,alpha,cuti',
            'keterangan' => 'nullable|string',
        ]);

        // Cek apakah sudah ada absensi untuk tanggal ini
        $existing = Absensi::where('karyawan_id', $validated['karyawan_id'])
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Absensi untuk karyawan ini pada tanggal tersebut sudah ada.');
        }

        $validated['diverifikasi_oleh'] = auth()->id();
        $validated['diverifikasi_at'] = now();
        $validated['status_verifikasi'] = 'terverifikasi';

        Absensi::create($validated);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil dicatat.');
    }

    /**
     * Update absensi (manual by admin)
     */
    public function update(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'jam_masuk' => 'nullable',
            'jam_keluar' => 'nullable',
            'status' => 'required|in:hadir,izin,sakit,alpha,cuti',
            'keterangan' => 'nullable|string',
        ]);

        $validated['diverifikasi_oleh'] = auth()->id();
        $validated['diverifikasi_at'] = now();

        $absensi->update($validated);

        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil diperbarui.');
    }

    /**
     * Delete absensi
     */
    public function destroy(Absensi $absensi)
    {
        // Delete photos if exist
        if ($absensi->foto_masuk) {
            $this->photoService->deletePhoto($absensi->foto_masuk);
        }
        if ($absensi->foto_keluar) {
            $this->photoService->deletePhoto($absensi->foto_keluar);
        }

        $absensi->delete();
        
        return redirect()->route('absensi.index')
            ->with('success', 'Absensi berhasil dihapus.');
    }

    /**
     * Check in (legacy method - will be replaced by scan)
     */
    public function checkIn(Request $request)
    {
        $karyawan = $this->currentKaryawan();
        
        if (!$karyawan instanceof Karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $today = Carbon::today();
        
        $absensi = Absensi::firstOrCreate(
            ['karyawan_id' => $karyawan->id, 'tanggal' => $today],
            ['status' => 'hadir', 'jam_masuk' => now()->format('H:i'), 'status_verifikasi' => 'pending']
        );

        if (!$absensi->wasRecentlyCreated) {
            return back()->with('error', 'Anda sudah check-in hari ini.');
        }

        return back()->with('success', 'Check-in berhasil.');
    }

    /**
     * Check out (legacy method - will be replaced by scan)
     */
    public function checkOut(Request $request)
    {
        $karyawan = $this->currentKaryawan();
        
        if (!$karyawan instanceof Karyawan) {
            return back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $today = Carbon::today();
        
        $absensi = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absensi) {
            return back()->with('error', 'Anda belum check-in hari ini.');
        }

        if ($absensi->jam_keluar) {
            return back()->with('error', 'Anda sudah check-out hari ini.');
        }

        $absensi->update(['jam_keluar' => now()->format('H:i')]);

        return back()->with('success', 'Check-out berhasil.');
    }

    private function currentKaryawan(): ?Karyawan
    {
        return Karyawan::where('user_id', auth()->id())->first();
    }
}
