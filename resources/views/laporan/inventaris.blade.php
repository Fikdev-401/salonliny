@extends('layouts.app')

@section('title', 'Laporan Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Inventaris</h4>
    <a href="{{ route('laporan.inventaris', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai, 'format' => 'pdf']) }}" class="btn btn-danger" target="_blank">
        <i class="bi bi-file-pdf"></i> PDF
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('laporan.inventaris') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="bagus" @selected(request('status') === 'bagus')>Bagus</option>
                    <option value="rusak" @selected(request('status') === 'rusak')>Rusak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Aset</label>
                <input type="text" name="jenis_aset" class="form-control" value="{{ request('jenis_aset') }}">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('laporan.inventaris') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Total Data Aset</div>
                <div class="fs-3 fw-semibold">{{ number_format($totalItem, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Total Unit</div>
                <div class="fs-3 fw-semibold">{{ number_format($totalUnit, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Status Bagus</div>
                <div class="fs-3 fw-semibold">{{ number_format($rekapStatus['bagus'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Status Rusak</div>
                <div class="fs-3 fw-semibold">{{ number_format($rekapStatus['rusak'] ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Rekap Jenis Aset</div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($rekapJenis as $jenis => $jumlah)
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">{{ $jenis }}</div>
                        <div class="fs-5 fw-semibold">{{ number_format($jumlah, 0, ',', '.') }} unit</div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">Belum ada data jenis aset.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Audit Inventaris</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Tanggal Input</th>
                        <th>Status</th>
                        <th>Cabang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventaris as $item)
                        <tr>
                            <td>{{ $item->kode_aset }}</td>
                            <td>{{ $item->nama_aset }}</td>
                            <td>{{ $item->jenis_aset }}</td>
                            <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ $item->tanggal_input->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>{{ $item->cabang->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Tidak ada data inventaris.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
