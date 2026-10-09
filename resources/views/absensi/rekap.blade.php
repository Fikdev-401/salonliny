@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Rekap Absensi Bulanan</h4>
    <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('absensi.rekap') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-filter"></i> Filter
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Karyawan</th>
                        <th>Hadir</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Alpha</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $item)
                    <tr>
                        <td>{{ $item['karyawan']->nama }}</td>
                        <td>{{ $item['hadir'] }}</td>
                        <td>{{ $item['izin'] }}</td>
                        <td>{{ $item['sakit'] }}</td>
                        <td>{{ $item['alpha'] }}</td>
                        <td>{{ $item['total'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data rekap</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
