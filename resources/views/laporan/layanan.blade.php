@extends('layouts.app')

@section('title', 'Laporan Layanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Layanan</h4>
    <div>
        <a href="{{ route('laporan.layanan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.layanan') }}" method="GET" class="row g-3">
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

<div class="card">
    <div class="card-header">
        Statistik Layanan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Layanan</th>
                        <th class="text-center">Jumlah Pesanan</th>
                        <th class="text-end">Total Pendapatan</th>
                        <th>Karyawan yang Mengerjakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['layanan']->nama }}</td>
                        <td class="text-center">{{ $item['jumlah'] }}</td>
                        <td class="text-end">{{ rupiah($item['total_pendapatan']) }}</td>
                        <td>
                            @if(count($item['workers']) > 0)
                                <small class="text-muted">{{ implode(', ', $item['workers']) }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
