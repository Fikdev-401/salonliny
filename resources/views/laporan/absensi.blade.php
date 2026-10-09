@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Absensi</h4>
    <div>
        <a href="{{ route('laporan.absensi', ['bulan' => $bulan, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.absensi') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
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
        Rekap Absensi Bulan {{ \Carbon\Carbon::parse($bulan . '-01')->format('F Y') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Karyawan</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Izin</th>
                        <th class="text-center">Sakit</th>
                        <th class="text-center">Alpha</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $item)
                    <tr>
                        <td>{{ $item['karyawan']->nama }}</td>
                        <td class="text-center"><span class="badge bg-success">{{ $item['hadir'] }}</span></td>
                        <td class="text-center"><span class="badge bg-warning">{{ $item['izin'] }}</span></td>
                        <td class="text-center"><span class="badge bg-info">{{ $item['sakit'] }}</span></td>
                        <td class="text-center"><span class="badge bg-danger">{{ $item['alpha'] }}</span></td>
                        <td class="text-center"><strong>{{ $item['total'] }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
