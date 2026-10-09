@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Data Karyawan</h4>
        <p class="page-subtitle">Kelola profil tenaga kerja, status aktif, dan referensi gaji pokok terkini.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('karyawan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Karyawan
        </a>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-tile">
        <span class="summary-label">Total Data</span>
        <div class="summary-value">{{ number_format($karyawans->total(), 0, ',', '.') }}</div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Pencarian</span>
        <div class="summary-value-sm">{{ request('search') ?: 'Semua karyawan' }}</div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Status</span>
        <div class="summary-value-sm">{{ request('status') ? ucfirst(request('status')) : 'Semua status' }}</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="section-heading">
            <div>
                <h5 class="section-title">Filter Karyawan</h5>
                <p class="section-caption">Cari karyawan cepat berdasarkan nama dan status kerja.</p>
            </div>
        </div>

        <form action="{{ route('karyawan.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Cari Nama</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status Kerja</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="cuti" {{ request('status') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-5 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-funnel"></i> Terapkan
                </button>
                <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle compact-table">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>No. HP</th>
                        <th>Gaji Pokok</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $karyawan)
                    <tr>
                        <td class="fw-semibold">{{ $karyawan->nama }}</td>
                        <td>{{ $karyawan->jabatan }}</td>
                        <td>{{ $karyawan->no_hp ?: '-' }}</td>
                        <td>{{ rupiah($karyawan->gaji_pokok) }}</td>
                        <td>
                            @if($karyawan->status_kerja == 'aktif')
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Aktif</span>
                            @elseif($karyawan->status_kerja == 'cuti')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Cuti</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('karyawan.show', $karyawan) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('karyawan.edit', $karyawan) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('karyawan.destroy', $karyawan) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan karyawan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Tidak ada data karyawan yang sesuai dengan filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $karyawans->links() }}
    </div>
</div>
@endsection
