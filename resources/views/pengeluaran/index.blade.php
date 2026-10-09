@extends('layouts.app')

@section('title', 'Pengeluaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Data Pengeluaran</h4>
    <a href="{{ route('pengeluaran.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Pengeluaran Manual
    </a>
</div>

<div class="card mb-4 bg-danger text-white">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-1">Total Pengeluaran</h6>
            <small>Manual dan otomatis dari pembelian/gaji.</small>
        </div>
        <h3 class="mb-0">{{ rupiah($totalPengeluaran) }}</h3>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('pengeluaran.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select">
                    <option value="">Semua</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori }}" @selected(request('kategori') === $kategori)>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Sumber</label>
                <select name="sumber" class="form-select">
                    <option value="">Semua</option>
                    <option value="manual" @selected(request('sumber') === 'manual')>Manual</option>
                    <option value="system" @selected(request('sumber') === 'system')>Otomatis</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Pengeluaran</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Nama</th>
                        <th>Jumlah</th>
                        <th>Sumber</th>
                        <th width="240">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluarans as $pengeluaran)
                        <tr>
                            <td>{{ $pengeluaran->nomor_pengeluaran }}</td>
                            <td>{{ $pengeluaran->tanggal->format('d/m/Y') }}</td>
                            <td>{{ $pengeluaran->kategori }}</td>
                            <td>{{ $pengeluaran->nama_pengeluaran }}</td>
                            <td class="fw-semibold">{{ rupiah($pengeluaran->jumlah) }}</td>
                            <td>
                                @if($pengeluaran->is_system_generated)
                                    <span class="badge bg-warning text-dark">Otomatis</span>
                                @else
                                    <span class="badge bg-secondary">Manual</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('pengeluaran.show', $pengeluaran) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin() && !$pengeluaran->is_system_generated)
                                        <a href="{{ route('pengeluaran.edit', $pengeluaran) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('pengeluaran.destroy', $pengeluaran) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada data pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pengeluarans->links() }}
    </div>
</div>
@endsection
