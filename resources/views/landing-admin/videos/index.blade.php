@extends('layouts.app')

@section('title', 'Video Landing Page')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Video Landing Page</h1>
        <p class="page-subtitle">Video showcase yang tampil di section Video halaman utama.</p>
    </div>
    <a href="{{ route('landing-admin.videos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Video
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
        @if($videos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Preview</th>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($videos as $video)
                        <tr>
                            <td>{{ $video->urutan }}</td>
                            <td>
                                @if($video->thumbnail_url)
                                    <img src="{{ $video->thumbnail_url }}" alt="" style="width: 80px; height: 45px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 45px; border-radius: 4px;">
                                        <i class="bi bi-film text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $video->judul }}</strong>
                                @if($video->deskripsi)<br><small class="text-muted">{{ Str::limit($video->deskripsi, 60) }}</small>@endif
                            </td>
                            <td>
                                @if($video->tipe === 'youtube')
                                    <span class="badge bg-danger"><i class="bi bi-youtube"></i> YouTube</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-upload"></i> Upload</span>
                                @endif
                            </td>
                            <td>
                                @if($video->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('landing-admin.videos.edit', $video) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('landing-admin.videos.destroy', $video) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus video ini?')">
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
                <i class="bi bi-play-btn" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="mt-3 mb-0">Belum ada video. <a href="{{ route('landing-admin.videos.create') }}">Tambah video pertama</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection
