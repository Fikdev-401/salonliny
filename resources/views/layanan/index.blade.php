@extends('layouts.app')

@section('title', 'Data Layanan')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Data Layanan</h4>
        <p class="page-subtitle">Kelola daftar layanan salon, kategori, harga default, dan status operasional.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('layanan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Layanan
        </a>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-tile">
        <span class="summary-label">Total Data</span>
        <div class="summary-value">{{ number_format($layanans->total(), 0, ',', '.') }}</div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Pencarian</span>
        <div class="summary-value-sm">{{ request('search') ?: 'Semua layanan' }}</div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Kategori</span>
        <div class="summary-value-sm">{{ request('kategori') ?: 'Semua kategori' }}</div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="section-heading">
            <div>
                <h5 class="section-title">Filter Layanan</h5>
                <p class="section-caption">Gunakan filter untuk mempersempit daftar layanan yang ditampilkan.</p>
            </div>
        </div>

        <form action="{{ route('layanan.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-5">
                <label class="form-label">Cari Nama</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama layanan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary">
                    <i class="bi bi-funnel"></i> Terapkan
                </button>
                <a href="{{ route('layanan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle compact-table">
                <thead class="table-light">
                    <tr>
                        <th>Nama Layanan</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($layanans as $layanan)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $layanan->nama }}</div>
                            @if($layanan->harga_fleksibel)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1">Harga Fleksibel</span>
                            @endif
                        </td>
                        <td>{{ $layanan->kategori ?? '-' }}</td>
                        <td>{{ rupiah($layanan->harga_default) }}</td>
                        <td>{{ $layanan->durasi_menit ? $layanan->durasi_menit . ' menit' : '-' }}</td>
                        <td>
                            @if($layanan->status == 'aktif')
                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Aktif</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('layanan.show', $layanan) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('layanan.edit', $layanan) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('layanan.destroy', $layanan) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan layanan ini?')">
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
                        <td colspan="6" class="text-center py-5 text-muted">Tidak ada data layanan yang sesuai dengan filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $layanans->links() }}
    </div>
</div>
@endsection
