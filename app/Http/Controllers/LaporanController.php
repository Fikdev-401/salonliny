<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\Absensi;
use App\Models\Gaji;
use App\Models\Inventaris;
use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function pendapatan(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $format = $request->input('format', 'html');

        $startDate = Carbon::parse($tanggalMulai)->startOfDay();
        $endDate = Carbon::parse($tanggalSelesai)->endOfDay();

        $pendapatanPerTanggal = Transaksi::query()
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status_pembayaran', 'lunas')
            ->select(
                DB::raw('DATE(tanggal_transaksi) as tanggal'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total) as total_pendapatan')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $detailTransaksi = Transaksi::with(['pelanggan', 'kasir'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status_pembayaran', 'lunas')
            ->orderByDesc('tanggal_transaksi')
            ->get();

        $totalPendapatan = $pendapatanPerTanggal->sum('total_pendapatan');
        $totalTransaksi = $pendapatanPerTanggal->sum('jumlah_transaksi');

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.pendapatan-pdf', compact(
                'pendapatanPerTanggal',
                'detailTransaksi',
                'tanggalMulai',
                'tanggalSelesai',
                'totalPendapatan',
                'totalTransaksi'
            ));

            return $pdf->stream('laporan-pendapatan.pdf');
        }

        if ($format === 'excel') {
            return Excel::download(new LaporanExport($detailTransaksi, 'pendapatan'), 'laporan-pendapatan.xlsx');
        }

        return view('laporan.pendapatan', compact(
            'pendapatanPerTanggal',
            'detailTransaksi',
            'tanggalMulai',
            'tanggalSelesai',
            'totalPendapatan',
            'totalTransaksi'
        ));
    }

    public function pengeluaran(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $format = $request->input('format', 'html');

        $query = Pengeluaran::with('user')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        $this->applyCabangScopeToPengeluaranQuery($query);

        $pengeluaran = $query->orderBy('tanggal')->get();
        $totalPengeluaran = $pengeluaran->sum('jumlah');

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.pengeluaran-pdf', compact('pengeluaran', 'tanggalMulai', 'tanggalSelesai', 'totalPengeluaran'));

            return $pdf->stream('laporan-pengeluaran.pdf');
        }

        return view('laporan.pengeluaran', compact('pengeluaran', 'tanggalMulai', 'tanggalSelesai', 'totalPengeluaran'));
    }

    public function transaksi(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $format = $request->input('format', 'html');

        $startDate = Carbon::parse($tanggalMulai)->startOfDay();
        $endDate = Carbon::parse($tanggalSelesai)->endOfDay();

        $transaksis = Transaksi::with(['pelanggan', 'kasir', 'details.karyawanUtama', 'details.karyawanPendukung1', 'details.karyawanPendukung2'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->orderByDesc('tanggal_transaksi')
            ->get();

        $totalPendapatan = $transaksis->where('status_pembayaran', 'lunas')->sum('total');

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.transaksi-pdf', compact('transaksis', 'tanggalMulai', 'tanggalSelesai', 'totalPendapatan'));

            return $pdf->stream('laporan-transaksi.pdf');
        }

        return view('laporan.transaksi', compact('transaksis', 'tanggalMulai', 'tanggalSelesai', 'totalPendapatan'));
    }

    public function labaRugi(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $format = $request->input('format', 'html');

        $startDate = Carbon::parse($tanggalMulai)->startOfDay();
        $endDate = Carbon::parse($tanggalSelesai)->endOfDay();

        $pendapatanTransaksi = Transaksi::query()
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status_pembayaran', 'lunas')
            ->sum('total');

        $pengeluaranQuery = Pengeluaran::query()->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        $this->applyCabangScopeToPengeluaranQuery($pengeluaranQuery);

        $pengeluaranBiaya = (clone $pengeluaranQuery)
            ->whereNull('sumber_tipe')
            ->sum('jumlah');
        $pembelianProduk = (clone $pengeluaranQuery)
            ->where('sumber_tipe', Pembelian::class)
            ->sum('jumlah');
        $pengeluaranGaji = (clone $pengeluaranQuery)
            ->where('sumber_tipe', Gaji::class)
            ->sum('jumlah');
        $totalPengeluaran = $pengeluaranBiaya + $pembelianProduk + $pengeluaranGaji;
        $labaRugi = $pendapatanTransaksi - $totalPengeluaran;

        $rincian = [
            ['nama' => 'Pendapatan Transaksi', 'jumlah' => $pendapatanTransaksi, 'tipe' => 'pendapatan'],
            ['nama' => 'Pengeluaran Operasional Manual', 'jumlah' => $pengeluaranBiaya, 'tipe' => 'pengeluaran'],
            ['nama' => 'Pembelian', 'jumlah' => $pembelianProduk, 'tipe' => 'pengeluaran'],
            ['nama' => 'Gaji & Insentif', 'jumlah' => $pengeluaranGaji, 'tipe' => 'pengeluaran'],
        ];

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.laba-rugi-pdf', compact(
                'pendapatanTransaksi',
                'pengeluaranBiaya',
                'pembelianProduk',
                'pengeluaranGaji',
                'totalPengeluaran',
                'labaRugi',
                'rincian',
                'tanggalMulai',
                'tanggalSelesai'
            ));

            return $pdf->stream('laporan-laba-rugi.pdf');
        }

        return view('laporan.laba-rugi', compact(
            'pendapatanTransaksi',
            'pengeluaranBiaya',
            'pembelianProduk',
            'pengeluaranGaji',
            'totalPengeluaran',
            'labaRugi',
            'rincian',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function absensi(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $format = $request->input('format', 'html');

        $startOfMonth = Carbon::parse($bulan . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($bulan . '-01')->endOfMonth();

        $karyawans = Karyawan::query()->where('status_kerja', 'aktif')->get();
        $rekap = [];

        foreach ($karyawans as $karyawan) {
            $absensiBulan = Absensi::where('karyawan_id', $karyawan->id)
                ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->get();

            $hadir = $absensiBulan->where('status', 'hadir')->count();
            $izin = $absensiBulan->where('status', 'izin')->count();
            $sakit = $absensiBulan->where('status', 'sakit')->count();
            $alpha = $absensiBulan->where('status', 'alpha')->count();

            $rekap[] = [
                'karyawan' => $karyawan,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total' => $hadir + $izin + $sakit + $alpha,
            ];
        }

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.absensi-pdf', compact('rekap', 'bulan'));

            return $pdf->stream('laporan-absensi.pdf');
        }

        return view('laporan.absensi', compact('rekap', 'bulan'));
    }

    public function gaji(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));
        $format = $request->input('format', 'html');

        $query = Gaji::with('karyawan')->where('periode', $periode);
        $this->applyCabangScopeToGajiQuery($query);

        $gajis = $query->orderByDesc('created_at')->get();
        $totalGaji = $gajis->sum('total_gaji');

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.gaji-pdf', compact('gajis', 'periode', 'totalGaji'));

            return $pdf->stream('laporan-gaji.pdf');
        }

        return view('laporan.gaji', compact('gajis', 'periode', 'totalGaji'));
    }

    public function layanan(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $format = $request->input('format', 'html');
        $startDate = Carbon::parse($tanggalMulai)->startOfDay();
        $endDate = Carbon::parse($tanggalSelesai)->endOfDay();

        $layanans = Layanan::where('status', 'aktif')->get();
        $laporan = [];

        foreach ($layanans as $layanan) {
            $details = TransaksiDetail::with(['karyawanUtama', 'karyawanPendukung1', 'karyawanPendukung2'])
                ->where('layanan_id', $layanan->id)
                ->whereHas('transaksi', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                        ->where('status', '!=', 'batal');
                })
                ->get();

            $jumlah = $details->sum('jumlah');
            $totalPendapatan = $details->sum('total');

            if ($jumlah <= 0) {
                continue;
            }

            $workers = [];
            foreach ($details as $detail) {
                if ($detail->karyawanUtama) {
                    $workers[$detail->karyawanUtama->id] = $detail->karyawanUtama->nama;
                }
                if ($detail->karyawanPendukung1) {
                    $workers[$detail->karyawanPendukung1->id] = $detail->karyawanPendukung1->nama;
                }
                if ($detail->karyawanPendukung2) {
                    $workers[$detail->karyawanPendukung2->id] = $detail->karyawanPendukung2->nama;
                }
            }

            $laporan[] = [
                'layanan' => $layanan,
                'jumlah' => $jumlah,
                'total_pendapatan' => $totalPendapatan,
                'workers' => array_values($workers),
            ];
        }

        usort($laporan, fn ($a, $b) => $b['jumlah'] <=> $a['jumlah']);

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.layanan-pdf', compact('laporan', 'tanggalMulai', 'tanggalSelesai'));

            return $pdf->stream('laporan-layanan.pdf');
        }

        return view('laporan.layanan', compact('laporan', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function performaKaryawanDetail(int $karyawanId, Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($bulan . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($bulan . '-01')->endOfMonth();

        $karyawan = Karyawan::findOrFail($karyawanId);

        $details = TransaksiDetail::with(['transaksi.pelanggan', 'layanan'])
            ->whereHas('transaksi', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
                    ->where('status', '!=', 'batal');
            })
            ->where(function ($query) use ($karyawanId) {
                $query->where('karyawan_utama_id', $karyawanId)
                    ->orWhere('karyawan_pendukung_1_id', $karyawanId)
                    ->orWhere('karyawan_pendukung_2_id', $karyawanId);
            })
            ->orderByDesc('id')
            ->get()
            ->map(function (TransaksiDetail $detail) use ($karyawanId) {
                if ((int) $detail->karyawan_utama_id === $karyawanId) {
                    $detail->role = 'Utama';
                    $detail->insentif = (float) $detail->insentif_utama;
                } elseif ((int) $detail->karyawan_pendukung_1_id === $karyawanId) {
                    $detail->role = 'Pendukung 1';
                    $detail->insentif = (float) $detail->insentif_pendukung_1;
                } else {
                    $detail->role = 'Pendukung 2';
                    $detail->insentif = (float) $detail->insentif_pendukung_2;
                }

                $detail->tanggal_transaksi = optional($detail->transaksi?->tanggal_transaksi)->format('Y-m-d H:i:s');

                return $detail;
            });

        return response()->json([
            'karyawan' => $karyawan,
            'bulan' => $bulan,
            'details' => $details,
            'total_insentif' => $details->sum('insentif'),
        ]);
    }

    public function performaKaryawan(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $format = $request->input('format', 'html');
        $selectedKaryawanId = $request->integer('karyawan_id');
        $startOfMonth = Carbon::parse($bulan . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($bulan . '-01')->endOfMonth();

        $karyawanQuery = Karyawan::query()->where('status_kerja', 'aktif')->orderBy('nama');
        if ($selectedKaryawanId) {
            $karyawanQuery->where('id', $selectedKaryawanId);
        }
        $karyawans = $karyawanQuery->get();

        $laporan = [];

        foreach ($karyawans as $karyawan) {
            $details = TransaksiDetail::with(['transaksi.pelanggan', 'layanan'])
                ->whereHas('transaksi', function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
                        ->where('status', '!=', 'batal');
                })
                ->where(function ($query) use ($karyawan) {
                    $query->where('karyawan_utama_id', $karyawan->id)
                        ->orWhere('karyawan_pendukung_1_id', $karyawan->id)
                        ->orWhere('karyawan_pendukung_2_id', $karyawan->id);
                })
                ->get()
                ->map(function (TransaksiDetail $detail) use ($karyawan) {
                    if ((int) $detail->karyawan_utama_id === $karyawan->id) {
                        $detail->role = 'Utama';
                        $detail->insentif = (float) $detail->insentif_utama;
                    } elseif ((int) $detail->karyawan_pendukung_1_id === $karyawan->id) {
                        $detail->role = 'Pendukung 1';
                        $detail->insentif = (float) $detail->insentif_pendukung_1;
                    } else {
                        $detail->role = 'Pendukung 2';
                        $detail->insentif = (float) $detail->insentif_pendukung_2;
                    }

                    return $detail;
                });

            $laporan[] = [
                'karyawan' => $karyawan,
                'layanan_utama' => $details->where('role', 'Utama')->count(),
                'layanan_pendukung' => $details->whereIn('role', ['Pendukung 1', 'Pendukung 2'])->count(),
                'total_insentif' => $details->sum('insentif'),
                'details' => $details->sortByDesc(fn ($detail) => optional($detail->transaksi)->tanggal_transaksi)->values(),
            ];
        }

        usort($laporan, fn ($a, $b) => ($b['layanan_utama'] + $b['layanan_pendukung']) <=> ($a['layanan_utama'] + $a['layanan_pendukung']));

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.performa-karyawan-pdf', compact('laporan', 'bulan'));

            return $pdf->stream('laporan-performa-karyawan.pdf');
        }

        return view('laporan.performa-karyawan', [
            'laporan' => $laporan,
            'bulan' => $bulan,
            'karyawans' => Karyawan::query()->where('status_kerja', 'aktif')->orderBy('nama')->get(),
            'selectedKaryawanId' => $selectedKaryawanId,
        ]);
    }

    public function inventaris(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $format = $request->input('format', 'html');

        $query = Inventaris::with(['creator', 'cabang'])
            ->whereBetween('tanggal_input', [$tanggalMulai, $tanggalSelesai]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_aset')) {
            $query->where('jenis_aset', $request->jenis_aset);
        }

        $inventaris = $query->orderByDesc('tanggal_input')->get();
        $totalItem = $inventaris->count();
        $totalUnit = $inventaris->sum('jumlah');
        $rekapStatus = $inventaris->groupBy('status')->map->count();
        $rekapJenis = $inventaris->groupBy('jenis_aset')->map->sum('jumlah')->sortKeys();

        if ($format === 'pdf') {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('laporan.inventaris-pdf', compact(
                'inventaris',
                'tanggalMulai',
                'tanggalSelesai',
                'totalItem',
                'totalUnit',
                'rekapStatus',
                'rekapJenis'
            ));

            return $pdf->stream('laporan-inventaris.pdf');
        }

        return view('laporan.inventaris', compact(
            'inventaris',
            'tanggalMulai',
            'tanggalSelesai',
            'totalItem',
            'totalUnit',
            'rekapStatus',
            'rekapJenis'
        ));
    }

    private function applyCabangScopeToPengeluaranQuery($query): void
    {
        $user = Auth::user();

        if (!$user || $user->isSuperAdmin()) {
            return;
        }

        $query->whereHas('user', function ($builder) use ($user) {
            if ($user->cabang_id) {
                $builder->where('cabang_id', $user->cabang_id);
                return;
            }

            $builder->whereNull('cabang_id');
        });
    }

    private function applyCabangScopeToGajiQuery($query): void
    {
        $user = Auth::user();

        if (!$user || $user->isSuperAdmin()) {
            return;
        }

        $query->whereHas('karyawan', function ($builder) use ($user) {
            if ($user->cabang_id) {
                $builder->where('cabang_id', $user->cabang_id);
                return;
            }

            $builder->whereNull('cabang_id');
        });
    }
}
