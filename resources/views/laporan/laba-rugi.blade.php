@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Laba Rugi</h4>
    <div>
        <a href="{{ route('laporan.laba-rugi', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.laba-rugi') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Pendapatan</h6>
                <h4 class="mb-0">{{ rupiah($pendapatanTransaksi) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Total Pengeluaran</h6>
                <h4 class="mb-0">{{ rupiah($totalPengeluaran) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card {{ $labaRugi >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
            <div class="card-body">
                <h6 class="card-title">{{ $labaRugi >= 0 ? 'Laba Bersih' : 'Rugi' }}</h6>
                <h4 class="mb-0">{{ rupiah(abs($labaRugi)) }}</h4>
                <small>{{ $labaRugi >= 0 ? 'Keuntungan' : 'Kerugian' }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pendapatan -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <i class="bi bi-graph-up-arrow"></i> Rincian Pendapatan
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Transaksi (Lunas)</td>
                            <td class="text-end">{{ rupiah($pendapatanTransaksi) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total Pendapatan</th>
                            <th class="text-end">{{ rupiah($pendapatanTransaksi) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Pengeluaran -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-graph-down-arrow"></i> Rincian Pengeluaran
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Keterangan</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Biaya Operasional</td>
                            <td class="text-end">{{ rupiah($pengeluaranBiaya) }}</td>
                        </tr>
                        <tr>
                            <td>Pembelian Produk</td>
                            <td class="text-end">{{ rupiah($pembelianProduk) }}</td>
                        </tr>
                        <tr>
                            <td>Gaji Karyawan</td>
                            <td class="text-end">{{ rupiah($pengeluaranGaji) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total Pengeluaran</th>
                            <th class="text-end">{{ rupiah($totalPengeluaran) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Calculation Summary -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-calculator"></i> Perhitungan Laba Rugi
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td width="50%">Total Pendapatan</td>
                <td class="text-end">{{ rupiah($pendapatanTransaksi) }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-end text-danger">- {{ rupiah($totalPengeluaran) }}</td>
            </tr>
            <tr class="border-top">
                <th>{{ $labaRugi >= 0 ? 'LABA BERSIH' : 'RUGI' }}</th>
                <th class="text-end {{ $labaRugi >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ rupiah(abs($labaRugi)) }}
                </th>
            </tr>
        </table>
    </div>
</div>
@endsection
