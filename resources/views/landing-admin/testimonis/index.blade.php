@extends('layouts.app')

@section('title', 'Testimoni Landing Page')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Testimoni Landing Page</h1>
        <p class="page-subtitle">Testimoni pelanggan yang tampil di section Testimoni halaman utama.</p>
    </div>
    <a href="{{ route('landing-admin.testimonis.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Testimoni
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($testimonis->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama / Jabatan</th>
                            <th>Rating</th>
                            <th>Pesan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testimonis as $testimoni)
                        <tr>
                            <td>{{ $testimoni->urutan }}</td>
                            <td>
                                @if($testimoni->foto_url)
                                    <img src="{{ $testimoni->foto_url }}" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                @else
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%; font-family: serif; font-size: 1.2rem;">
                                        {{ mb_substr($testimoni->nama, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $testimoni->nama }}</strong>
                                @if($testimoni->jabatan)<br><small class="text-muted">{{ $testimoni->jabatan }}</small>@endif
                            </td>
                            <td>
                                <span class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $testimoni->rating)<i class="bi bi-star-fill"></i>@else<i class="bi bi-star"></i>@endif
                                    @endfor
                                </span>
                            </td>
                            <td><small class="text-muted">{{ Str::limit($testimoni->pesan, 70) }}</small></td>
                            <td>
                                @if($testimoni->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('landing-admin.testimonis.edit', $testimoni) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('landing-admin.testimonis.destroy', $testimoni) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus testimoni ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-chat-quote" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3 mb-0">Belum ada testimoni. <a href="{{ route('landing-admin.testimonis.create') }}">Tambah testimoni pertama</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection
