<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Karyawan;
use App\Models\Pengeluaran;
use App\Models\Produk;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Statistik hari ini
        $transaksiHariIni = Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status_pembayaran', 'lunas')
            ->count();
        
        $pendapatanHariIni = Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status_pembayaran', 'lunas')
            ->sum('total');

        // Statistik bulan ini
        $pendapatanBulanIni = Transaksi::whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->where('status_pembayaran', 'lunas')
            ->sum('total');

        $pengeluaranBulanIni = Pengeluaran::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        // Total data
        $totalPelanggan = Pelanggan::where('status', 'aktif')->count();
        $totalKaryawan = Karyawan::where('status_kerja', 'aktif')->count();
        $produkMenipis = Produk::whereColumn('stok', '<=', 'stok_minimal')->count();

        // Transaksi terbaru
        $transaksiTerbaru = Transaksi::with(['pelanggan', 'kasir'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk chart pendapatan 7 hari terakhir
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartData[] = Transaksi::whereDate('tanggal_transaksi', $date)
                ->where('status_pembayaran', 'lunas')
                ->sum('total');
        }

        return view('dashboard.index', compact(
            'transaksiHariIni',
            'pendapatanHariIni',
            'pendapatanBulanIni',
            'pengeluaranBulanIni',
            'totalPelanggan',
            'totalKaryawan',
            'produkMenipis',
            'transaksiTerbaru',
            'chartLabels',
            'chartData'
        ));
    }
}
