@extends('layouts.app')

@section('title', 'Performa Karyawan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Performa Karyawan & Rekap Insentif</h4>
    <a href="{{ route('laporan.performa', ['bulan' => $bulan, 'karyawan_id' => $selectedKaryawanId, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
        <i class="bi bi-file-pdf"></i> PDF
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.performa') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Karyawan</label>
                <select name="karyawan_id" class="form-select">
                    <option value="">Semua Karyawan</option>
                    @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" @selected((string) $selectedKaryawanId === (string) $karyawan->id)>{{ $karyawan->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('laporan.performa') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Jumlah Karyawan</div>
                <div class="fs-3 fw-semibold">{{ count($laporan) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Total Insentif Bulan Ini</div>
                <div class="fs-3 fw-semibold">{{ rupiah(collect($laporan)->sum('total_insentif')) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Total Layanan Dikerjakan</div>
                <div class="fs-3 fw-semibold">{{ collect($laporan)->sum(fn ($item) => $item['layanan_utama'] + $item['layanan_pendukung']) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Ringkasan Bulan {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Karyawan</th>
                        <th>Jabatan</th>
                        <th class="text-center">Layanan Utama</th>
                        <th class="text-center">Layanan Pendukung</th>
                        <th class="text-center">Total Layanan</th>
                        <th class="text-end">Total Insentif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['karyawan']->nama }}</td>
                            <td>{{ $item['karyawan']->jabatan }}</td>
                            <td class="text-center">{{ $item['layanan_utama'] }}</td>
                            <td class="text-center">{{ $item['layanan_pendukung'] }}</td>
                            <td class="text-center fw-semibold">{{ $item['layanan_utama'] + $item['layanan_pendukung'] }}</td>
                            <td class="text-end fw-semibold">{{ rupiah($item['total_insentif']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada data performa untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($laporan as $item)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ $item['karyawan']->nama }} - {{ $item['karyawan']->jabatan }}</span>
            <span class="fw-semibold">{{ rupiah($item['total_insentif']) }}</span>
        </div>
        <div class="card-body">
            @if($item['details']->isEmpty())
                <div class="text-muted">Tidak ada transaksi sumber insentif.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Transaksi</th>
                                <th>Pelanggan</th>
                                <th>Item</th>
                                <th>Peran</th>
                                <th class="text-end">Insentif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item['details'] as $detail)
                                <tr>
                                    <td>{{ $detail->transaksi->tanggal_transaksi?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ $detail->transaksi->nomor_transaksi ?? '-' }}</td>
                                    <td>{{ $detail->transaksi->pelanggan->nama ?? '-' }}</td>
                                    <td>{{ $detail->nama_item }}</td>
                                    <td>{{ $detail->role }}</td>
                                    <td class="text-end">{{ rupiah($detail->insentif) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endforeach
@endsection
