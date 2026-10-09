@extends('layouts.app')

@section('title', 'Tambah Layanan')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Tambah Layanan</h4>
        <p class="page-subtitle">Buat layanan baru dengan kategori, durasi, dan aturan harga yang jelas.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('layanan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="section-heading">
            <div>
                <h5 class="section-title">Form Layanan</h5>
                <p class="section-caption">Harga default dipakai otomatis, kecuali layanan ditandai fleksibel.</p>
            </div>
        </div>

        <form action="{{ route('layanan.store') }}" method="POST">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Layanan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control @error('kategori') is-invalid @enderror" value="{{ old('kategori') }}" placeholder="Contoh: Potong, Perawatan, Coloring">
                    @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="2">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="harga_fleksibel" class="form-check-input" id="harga_fleksibel" value="1" {{ old('harga_fleksibel') ? 'checked' : '' }}>
                        <label class="form-check-label" for="harga_fleksibel">
                            Harga Fleksibel (bisa diubah saat transaksi)
                        </label>
                    </div>
                    <small class="text-muted">Centang jika harga layanan bisa dinegosiasikan sesuai kondisi.</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Durasi (menit)</label>
                    <input type="number" name="durasi_menit" class="form-control @error('durasi_menit') is-invalid @enderror" value="{{ old('durasi_menit') }}" min="1">
                    @error('durasi_menit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Default <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="harga_default" class="form-control @error('harga_default') is-invalid @enderror" value="{{ old('harga_default', 0) }}" min="0" required>
                    </div>
                    @error('harga_default')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('layanan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
