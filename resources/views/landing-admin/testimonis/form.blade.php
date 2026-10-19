@extends('layouts.app')

@section('title', $testimoni->exists ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $testimoni->exists ? 'Edit Testimoni' : 'Tambah Testimoni' }}</h1>
        <p class="page-subtitle">{{ $testimoni->exists ? 'Perbarui testimoni.' : 'Tambah testimoni pelanggan baru.' }}</p>
    </div>
    <a href="{{ route('landing-admin.testimonis.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ $testimoni->exists ? route('landing-admin.testimonis.update', $testimoni) : route('landing-admin.testimonis.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($testimoni->exists) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $testimoni->nama) }}" maxlength="255" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Jabatan / Keterangan</label>
                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $testimoni->jabatan) }}" maxlength="255" placeholder="cth: Pelanggan Setia, Model, dll">
            </div>
            <div class="mb-3">
                <label class="form-label">Pesan Testimoni <span class="text-danger">*</span></label>
                <textarea name="pesan" class="form-control @error('pesan') is-invalid @enderror" rows="4" required placeholder="Tulis testimoni pelanggan di sini...">{{ old('pesan', $testimoni->pesan) }}</textarea>
                @error('pesan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Rating <span class="text-danger">*</span></label>
                <div class="d-flex gap-3 align-items-center">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating', $testimoni->rating ?? 5) == $i ? 'checked' : '' }} required>
                        <label class="form-check-label" for="rating{{ $i }}" style="cursor: pointer;">
                            {{ $i }} <i class="bi bi-star-fill text-warning"></i>
                        </label>
                    </div>
                    @endfor
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto (opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                @if($testimoni->foto_url)
                    <div class="mt-2"><img src="{{ $testimoni->foto_url }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 2px solid var(--liny-gold);"></div>
                @endif
                <small class="text-muted">Format: JPG, PNG, WEBP. Maks 4MB. Otomatis circular di landing page.</small>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $testimoni->urutan ?? 0) }}" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', $testimoni->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> {{ $testimoni->exists ? 'Simpan Perubahan' : 'Tambah Testimoni' }}
                </button>
                <a href="{{ route('landing-admin.testimonis.index') }}" class="btn btn-light ms-2">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
