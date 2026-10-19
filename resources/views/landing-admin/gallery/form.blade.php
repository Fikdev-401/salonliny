@extends('layouts.app')

@section('title', $gallery->exists ? 'Edit Foto' : 'Tambah Foto')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $gallery->exists ? 'Edit Foto Galeri' : 'Tambah Foto Galeri' }}</h1>
        <p class="page-subtitle">{{ $gallery->exists ? 'Perbarui detail foto.' : 'Upload foto baru untuk ditampilkan di section Galeri.' }}</p>
    </div>
    <a href="{{ route('landing-admin.gallery.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ $gallery->exists ? route('landing-admin.gallery.update', $gallery) : route('landing-admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($gallery->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Foto <span class="text-danger">*</span></label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/jpg,image/png,image/webp" {{ $gallery->exists ? '' : 'required' }}>
                @if($gallery->image_path)
                    <div class="mt-2"><img src="{{ $gallery->image_url }}" style="max-height: 200px; border-radius: 8px;"></div>
                @endif
                <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks 4MB.</small>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control" value="{{ old('judul', $gallery->judul) }}" maxlength="255" placeholder="cth: Hair Coloring Result">
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi singkat (opsional)">{{ old('deskripsi', $gallery->deskripsi) }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $gallery->urutan ?? 0) }}" min="0">
                    <small class="text-muted">Angka lebih kecil tampil lebih dulu.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', $gallery->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Aktif (tampil di landing page)</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ $gallery->exists ? 'Simpan Perubahan' : 'Tambah Foto' }}
                </button>
                <a href="{{ route('landing-admin.gallery.index') }}" class="btn btn-light ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
