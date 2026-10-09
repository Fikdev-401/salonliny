@extends('layouts.app')

@section('title', 'Konfigurasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Konfigurasi Sistem</h4>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('konfigurasi.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_perusahaan" class="form-control" value="{{ $konfigurasi->nama_perusahaan }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" value="{{ $konfigurasi->no_telepon }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $konfigurasi->email }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pajak (%)</label>
                    <input type="number" name="pajak_persen" class="form-control" value="{{ $konfigurasi->pajak_persen }}" min="0" max="100" step="0.01">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jam Buka</label>
                    <input type="time" name="jam_buka" class="form-control" value="{{ $konfigurasi->jam_buka }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jam Tutup</label>
                    <input type="time" name="jam_tutup" class="form-control" value="{{ $konfigurasi->jam_tutup }}">
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ $konfigurasi->alamat }}</textarea>
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    @if($konfigurasi->logo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/konfigurasi/' . $konfigurasi->logo) }}" alt="Logo" style="max-height: 100px;">
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
