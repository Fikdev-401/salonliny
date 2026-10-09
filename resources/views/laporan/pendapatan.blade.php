@extends('layouts.app')

@section('title', 'Laporan Pendapatan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Pendapatan</h4>
    <div>
        <a href="{{ route('laporan.pendapatan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
        <a href="{{ route('laporan.pendapatan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'format' => 'excel']) }}" class="btn btn-success">
            <i class="bi bi-file-excel"></i> Excel
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.pendapatan') }}" method="GET" class="row g-3">
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
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Pendapatan</h5>
                <h3>{{ rupiah($totalPendapatan) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total Transaksi</h5>
                <h3>{{ number_format($totalTransaksi, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Summary Per Tanggal -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-calendar"></i> Ringkasan Harian
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jumlah Transaksi</th>
                        <th class="text-end">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendapatanPerTanggal as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $item->jumlah_transaksi }}</td>
                        <td class="text-end">{{ rupiah($item->total_pendapatan) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th>{{ number_format($totalTransaksi, 0, ',', '.') }}</th>
                        <th class="text-end">{{ rupiah($totalPendapatan) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Detail Transaksi -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-receipt"></i> Detail Daftar Transaksi
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Metode</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detailTransaksi as $transaksi)
                    <tr>
                        <td>{{ $transaksi->nomor_transaksi }}</td>
                        <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                        <td>{{ $transaksi->pelanggan->nama ?? '-' }}</td>
                        <td>{{ $transaksi->kasir->nama ?? '-' }}</td>
                        <td>{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                        <td class="text-end">{{ rupiah($transaksi->total) }}</td>
                        <td>
                            @if($transaksi->status_pembayaran == 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">Tidak ada data transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
