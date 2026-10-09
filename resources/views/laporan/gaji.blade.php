@extends('layouts.app')

@section('title', 'Laporan Gaji')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Gaji</h4>
    <div>
        <a href="{{ route('laporan.gaji', ['periode' => $periode, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.gaji') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Periode</label>
                <input type="month" name="periode" class="form-control" value="{{ $periode }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Gaji Periode {{ $periode }}</h5>
                <h3>{{ rupiah($totalGaji) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Detail Gaji Karyawan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Karyawan</th>
                        <th class="text-end">Gaji Pokok</th>
                        <th class="text-end">Insentif</th>
                        <th class="text-end">Bonus</th>
                        <th class="text-end">THR</th>
                        <th class="text-end">Potongan</th>
                        <th class="text-end">Total Gaji</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gajis as $item)
                    <tr>
                        <td>{{ $item->karyawan->nama }}</td>
                        <td class="text-end">{{ rupiah($item->gaji_pokok) }}</td>
                        <td class="text-end">{{ rupiah($item->total_insentif) }}</td>
                        <td class="text-end">{{ rupiah($item->bonus) }}</td>
                        <td class="text-end">{{ rupiah($item->thr ?? 0) }}</td>
                        <td class="text-end">{{ rupiah($item->potongan) }}</td>
                        <td class="text-end"><strong>{{ rupiah($item->total_gaji) }}</strong></td>
                        <td>
                            @if($item->status == 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @elseif($item->status == 'disetujui')
                                <span class="badge bg-info">Disetujui</span>
                            @elseif($item->status == 'dibayar')
                                <span class="badge bg-success">Dibayar</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th class="text-end">Total</th>
                        <th class="text-end">{{ rupiah($gajis->sum('gaji_pokok')) }}</th>
                        <th class="text-end">{{ rupiah($gajis->sum('total_insentif')) }}</th>
                        <th class="text-end">{{ rupiah($gajis->sum('bonus')) }}</th>
                        <th class="text-end">{{ rupiah($gajis->sum('thr')) }}</th>
                        <th class="text-end">{{ rupiah($gajis->sum('potongan')) }}</th>
                        <th class="text-end">{{ rupiah($totalGaji) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
