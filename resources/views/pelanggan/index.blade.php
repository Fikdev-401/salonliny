@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Data Pelanggan</h4>
    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Pelanggan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter -->
        <form action="{{ route('pelanggan.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau nomor HP..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>No. HP</th>
                        <th>Tgl Lahir</th>
                        <th>Kunjungan</th>
                        <th>Terakhir</th>
                        <th>Waktu Registrasi</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggans as $pelanggan)
                    <tr>
                        <td>{{ $pelanggan->nama }}</td>
                        <td>{{ $pelanggan->no_hp }}</td>
                        <td>{{ $pelanggan->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $pelanggan->total_kunjungan }}x</td>
                        <td>{{ $pelanggan->terakhir_kunjungan?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $pelanggan->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($pelanggan->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pelanggan.show', $pelanggan) }}" class="btn btn-sm btn-info btn-action">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('pelanggan.destroy', $pelanggan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menonaktifkan pelanggan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-action">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">Tidak ada data pelanggan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pelanggans->links() }}
    </div>
</div>
@endsection
