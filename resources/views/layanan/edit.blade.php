@extends('layouts.app')

@section('title', 'Edit Layanan')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Edit Layanan</h4>
        <p class="page-subtitle">Perbarui harga, kategori, dan status layanan tanpa mengubah histori transaksi lama.</p>
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
                <h5 class="section-title">Form Perubahan Layanan</h5>
                <p class="section-caption">Gunakan status untuk menutup layanan dari operasional tanpa menghapus datanya.</p>
            </div>
        </div>

        <form action="{{ route('layanan.update', $layanan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Layanan <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $layanan->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input type="text" name="kategori" class="form-control @error('kategori') is-invalid @enderror" value="{{ old('kategori', $layanan->kategori) }}">
                    @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="2">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="harga_fleksibel" class="form-check-input" id="harga_fleksibel" value="1" {{ old('harga_fleksibel', $layanan->harga_fleksibel) ? 'checked' : '' }}>
                        <label class="form-check-label" for="harga_fleksibel">
                            Harga Fleksibel
                        </label>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Durasi (menit)</label>
                    <input type="number" name="durasi_menit" class="form-control @error('durasi_menit') is-invalid @enderror" value="{{ old('durasi_menit', $layanan->durasi_menit) }}" min="1">
                    @error('durasi_menit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Default <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="harga_default" class="form-control @error('harga_default') is-invalid @enderror" value="{{ old('harga_default', $layanan->harga_default) }}" min="0" required>
                    </div>
                    @error('harga_default')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="aktif" {{ old('status', $layanan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $layanan->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('layanan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
