@extends('layouts.app')

@section('title', 'Galeri Landing Page')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Galeri Landing Page</h1>
        <p class="page-subtitle">Foto hasil kerjaan yang tampil di section Galeri halaman utama.</p>
    </div>
    <a href="{{ route('landing-admin.gallery.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Foto
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
        @if($galleries->count() > 0)
            <div class="row g-3">
                @foreach($galleries as $gallery)
                <div class="col-md-4 col-lg-3">
                    <div class="position-relative border rounded overflow-hidden" style="aspect-ratio: 1;">
                        <img src="{{ $gallery->image_url }}" alt="{{ $gallery->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="position-absolute top-0 start-0 m-2 d-flex gap-1">
                            @if($gallery->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                            <span class="badge bg-dark">#{{ $gallery->urutan }}</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        @if($gallery->judul)<strong class="d-block text-truncate" title="{{ $gallery->judul }}">{{ $gallery->judul }}</strong>@endif
                        @if($gallery->deskripsi)<small class="text-muted d-block text-truncate" title="{{ $gallery->deskripsi }}">{{ $gallery->deskripsi }}</small>@endif
                        <div class="mt-2 d-flex gap-1">
                            <a href="{{ route('landing-admin.gallery.edit', $gallery) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('landing-admin.gallery.destroy', $gallery) }}" method="POST" class="flex-fill" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-images" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3 mb-0">Belum ada foto. <a href="{{ route('landing-admin.gallery.create') }}">Tambah foto pertama</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection
