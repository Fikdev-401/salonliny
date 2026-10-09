@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Dashboard</h4>
    <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
</div>

<!-- Statistik Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card primary h-100">
            <div class="card-body">
                <div class="stat-label text-primary">Transaksi Hari Ini</div>
                <div class="stat-value">{{ $transaksiHariIni }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card success h-100">
            <div class="card-body">
                <div class="stat-label text-success">Pendapatan Hari Ini</div>
                <div class="stat-value">{{ rupiah($pendapatanHariIni) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card info h-100">
            <div class="card-body">
                <div class="stat-label text-info">Total Pelanggan</div>
                <div class="stat-value">{{ $totalPelanggan }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card warning h-100">
            <div class="card-body">
                <div class="stat-label text-warning">Total Karyawan</div>
                <div class="stat-value">{{ $totalKaryawan }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Pendapatan -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up"></i> Grafik Pendapatan 7 Hari Terakhir</span>
            </div>
            <div class="card-body">
                <canvas id="pendapatanChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Ringkasan Keuangan -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-cash-stack"></i> Ringkasan Bulan Ini
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Pendapatan</span>
                        <span class="text-success fw-bold">{{ rupiah($pendapatanBulanIni) }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Pengeluaran</span>
                        <span class="text-danger fw-bold">{{ rupiah($pengeluaranBulanIni) }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        @php
                            $persentasePengeluaran = $pendapatanBulanIni > 0 ? ($pengeluaranBulanIni / $pendapatanBulanIni) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-danger" style="width: {{ min($persentasePengeluaran, 100) }}%"></div>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Net Profit</span>
                    <span class="fw-bold {{ $pendapatanBulanIni - $pengeluaranBulanIni >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ rupiah($pendapatanBulanIni - $pengeluaranBulanIni) }}
                    </span>
                </div>
                
                @if($produkMenipis > 0)
                <div class="alert alert-warning mt-4 mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>{{ $produkMenipis }}</strong> produk stok menipis!
                    <a href="{{ route('produk.index', ['stok_status' => 'menipis']) }}" class="alert-link">Lihat</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transaksi Terbaru -->
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-clock-history"></i> Transaksi Terbaru
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
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiTerbaru as $transaksi)
                    <tr>
                        <td>{{ $transaksi->nomor_transaksi }}</td>
                        <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                        <td>{{ $transaksi->pelanggan->nama }}</td>
                        <td>{{ $transaksi->kasir->nama }}</td>
                        <td>{{ rupiah($transaksi->total) }}</td>
                        <td>
                            @if($transaksi->status_pembayaran == 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @elseif($transaksi->status_pembayaran == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Batal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('pendapatanChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pendapatan',
                data: @json($chartData),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return window.formatRupiah(value);
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
