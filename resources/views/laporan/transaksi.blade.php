@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Transaksi</h4>
    <div>
        <a href="{{ route('laporan.transaksi', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.transaksi') }}" method="GET" class="row g-3">
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

@php
    // Hitung ringkasan harian
    $ringkasanHarian = [];
    foreach($transaksis as $item) {
        $tanggal = $item->tanggal_transaksi->format('Y-m-d');
        if(!isset($ringkasanHarian[$tanggal])) {
            $ringkasanHarian[$tanggal] = [
                'jumlah' => 0,
                'total' => 0
            ];
        }
        $ringkasanHarian[$tanggal]['jumlah']++;
        if($item->status_pembayaran == 'lunas') {
            $ringkasanHarian[$tanggal]['total'] += $item->total;
        }
    }
    $totalTransaksi = $transaksis->count();
@endphp

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
                <h3>{{ $totalTransaksi }}</h3>
            </div>
        </div>
    </div>
</div>

@if(count($ringkasanHarian) > 0)
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-calendar-day"></i> Ringkasan Harian
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-center">Jumlah Transaksi</th>
                        <th class="text-end">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ringkasanHarian as $tanggal => $data)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $data['jumlah'] }}</td>
                        <td class="text-end">{{ rupiah($data['total']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th class="text-center">{{ $totalTransaksi }}</th>
                        <th class="text-end">{{ rupiah($totalPendapatan) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

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
                        <th>Karyawan yang Mengerjakan</th>
                        <th>Metode</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $item)
                    @php
                        // Get unique workers from all details
                        $workers = [];
                        foreach($item->details as $detail) {
                            if($detail->karyawanUtama) {
                                $workers[] = 'Utama: ' . $detail->karyawanUtama->nama;
                            }
                            if($detail->karyawanPendukung1) {
                                $workers[] = 'Pendukung: ' . $detail->karyawanPendukung1->nama;
                            }
                            if($detail->karyawanPendukung2) {
                                $workers[] = 'Pendukung: ' . $detail->karyawanPendukung2->nama;
                            }
                        }
                        $workers = array_unique($workers);
                    @endphp
                    <tr>
                        <td>{{ $item->nomor_transaksi }}</td>
                        <td>{{ $item->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->pelanggan->nama ?? '-' }}</td>
                        <td>{{ $item->kasir->nama ?? '-' }}</td>
                        <td>
                            @if(count($workers) > 0)
                                <small>
                                    @foreach($workers as $worker)
                                        {{ $worker }}@if(!$loop->last)<br>@endif
                                    @endforeach
                                </small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($item->metode_pembayaran) }}</td>
                        <td class="text-end">{{ rupiah($item->total) }}</td>
                        <td>
                            @if($item->status_pembayaran == 'lunas')
                                <span class="badge bg-success">Lunas</span>
                            @elseif($item->status_pembayaran == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Batal</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('transaksi.struk', $item) }}" class="btn btn-sm btn-secondary" target="_blank" title="Cetak Struk">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Tidak ada data transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end">Total</th>
                        <th class="text-end">{{ rupiah($totalPendapatan) }}</th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
