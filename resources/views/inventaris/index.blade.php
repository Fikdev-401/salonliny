@extends('layouts.app')

@section('title', 'Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Inventaris Salon</h4>
    <a href="{{ route('inventaris.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Inventaris
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('inventaris.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Kode aset, nama aset, jenis aset">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="bagus" @selected(request('status') === 'bagus')>Bagus</option>
                    <option value="rusak" @selected(request('status') === 'rusak')>Rusak</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Tanggal Input</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
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
                            <td>
                                <span class="badge {{ $item->status === 'bagus' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('inventaris.show', $item) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('inventaris.edit', $item) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('inventaris.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus inventaris ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Belum ada data inventaris.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $inventaris->links() }}
    </div>
</div>
@endsection
