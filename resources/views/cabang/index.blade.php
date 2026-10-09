@extends('layouts.app')

@section('title', 'Manajemen Cabang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Manajemen Cabang</h4>
    <a href="{{ route('cabang.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Cabang
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Cabang</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Default</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cabangs as $cabang)
                    <tr>
                        <td>{{ $cabang->nama }}</td>
                        <td>{{ $cabang->alamat ?? '-' }}</td>
                        <td>{{ $cabang->no_telepon ?? '-' }}</td>
                        <td>{{ $cabang->email ?? '-' }}</td>
                        <td>
                            @if($cabang->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            @if($cabang->is_default)
                                <span class="badge bg-primary"><i class="bi bi-check"></i> Default</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('cabang.edit', $cabang) }}" class="btn btn-sm btn-warning btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if(!$cabang->is_default)
                            <form action="{{ route('cabang.destroy', $cabang) }}" method="POST" class="d-inline" onsubmit="return confirm('Nonaktifkan cabang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-action">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">Tidak ada data cabang</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
