@extends('layouts.app')

@section('title', $video->exists ? 'Edit Video' : 'Tambah Video')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $video->exists ? 'Edit Video' : 'Tambah Video' }}</h1>
        <p class="page-subtitle">{{ $video->exists ? 'Perbarui detail video.' : 'Tambah video YouTube atau upload file MP4.' }}</p>
    </div>
    <a href="{{ route('landing-admin.videos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ $video->exists ? route('landing-admin.videos.update', $video) : route('landing-admin.videos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($video->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $video->judul) }}" maxlength="255" required>
                @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $video->deskripsi) }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipe Video <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipe" id="tipeYoutube" value="youtube" {{ old('tipe', $video->tipe ?: 'youtube') === 'youtube' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tipeYoutube"><i class="bi bi-youtube text-danger"></i> YouTube</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipe" id="tipeUpload" value="upload" {{ old('tipe', $video->tipe) === 'upload' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tipeUpload"><i class="bi bi-upload"></i> Upload File</label>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="youtubeField">
                <label class="form-label">URL YouTube</label>
                <input type="text" name="youtube_url" class="form-control @error('youtube_url') is-invalid @enderror" value="{{ old('youtube_url', $video->youtube_id ? 'https://www.youtube.com/watch?v=' . $video->youtube_id : '') }}" placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/...">
                <small class="text-muted">Mendukung format: <code>youtube.com/watch?v=XXX</code>, <code>youtu.be/XXX</code>, atau <code>youtube.com/embed/XXX</code>.</small>
                @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="mt-2" id="youtubePreview"></div>
            </div>

            <div class="mb-3" id="uploadField" style="display: none;">
                <label class="form-label">File Video (MP4/WebM/OGG)</label>
                <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/ogg">
                @if($video->video_file)
                    <small class="text-muted d-block mt-1">File saat ini: <code>{{ $video->video_file }}</code></small>
                @endif
                <small class="text-muted">Maks 50MB. Disarankan kompres sebelum upload.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Thumbnail (opsional)</label>
                <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                @if($video->thumbnail)
                    <div class="mt-2"><img src="{{ asset('storage/' . $video->thumbnail) }}" style="max-height: 120px; border-radius: 6px;"></div>
                @elseif($video->youtube_id)
                    <small class="text-muted d-block mt-1">Thumbnail otomatis dari YouTube.</small>
                @endif
                <small class="text-muted">Untuk YouTube, thumbnail otomatis. Upload manual hanya untuk tipe upload.</small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $video->urutan ?? 0) }}" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', $video->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ $video->exists ? 'Simpan Perubahan' : 'Tambah Video' }}
                </button>
                <a href="{{ route('landing-admin.videos.index') }}" class="btn btn-light ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleFields() {
        const isYoutube = document.getElementById('tipeYoutube').checked;
        document.getElementById('youtubeField').style.display = isYoutube ? 'block' : 'none';
        document.getElementById('uploadField').style.display = isYoutube ? 'none' : 'block';
    }
    document.querySelectorAll('input[name="tipe"]').forEach(r => r.addEventListener('change', toggleFields));
    toggleFields();
</script>
@endpush
@endsection
