@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Pengaturan Sistem</h4>
</div>

<div class="row">
    <!-- Pengaturan Umum -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-gear"></i> Pengaturan Umum
            </div>
            <div class="card-body">
                <form action="{{ route('pengaturan.update') }}" method="POST" enctype="multipart/form-data">
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
                            <input type="number" name="pajak_persen" class="form-control" value="{{ $konfigurasi->pajak_persen }}" step="0.01" min="0" max="100">
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
                            <textarea name="alamat" class="form-control" rows="2">{{ $konfigurasi->alamat }}</textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            @if($konfigurasi->logo)
                                <small class="text-muted">Logo saat ini: {{ $konfigurasi->logo }}</small>
                            @endif
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Manajemen Cabang (Super Admin Only) -->
    @if(auth()->user()->isSuperAdmin())
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building"></i> Manajemen Cabang</span>
                <a href="{{ route('cabang.create') }}" class="btn btn-sm btn-light">
                    <i class="bi bi-plus"></i> Tambah Cabang
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>No. Telepon</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cabangs as $cabang)
                            <tr>
                                <td>{{ $cabang->nama }}</td>
                                <td>{{ $cabang->no_telepon ?? '-' }}</td>
                                <td>
                                    @if($cabang->status == 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cabang->is_default)
                                        <span class="badge bg-primary">Default</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('cabang.edit', $cabang) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data cabang</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if(auth()->user()->isSuperAdmin())
<div class="card">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-tags"></i> Master Jenis Produk
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Slug Sistem</th>
                        <th>Label</th>
                        <th>Status</th>
                        <th>Dipakai di Transaksi</th>
                        <th>Mempengaruhi Pemasukan</th>
                        <th width="280">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jenisProduks as $jenisProduk)
                        <tr>
                            <td><code>{{ $jenisProduk->slug }}</code></td>
                            <td>
                                <form action="{{ route('pengaturan.jenis-produk.update', $jenisProduk) }}" method="POST" class="row g-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="col-md-6">
                                        <input type="text" name="nama" class="form-control form-control-sm" value="{{ $jenisProduk->nama }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="aktif" @selected($jenisProduk->status === 'aktif')>Aktif</option>
                                            <option value="nonaktif" @selected($jenisProduk->status === 'nonaktif')>Nonaktif</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Simpan</button>
                                    </div>
                                </form>
                            </td>
                            <td>{{ ucfirst($jenisProduk->status) }}</td>
                            <td>{{ $jenisProduk->is_transactable ? 'Ya' : 'Tidak' }}</td>
                            <td>{{ $jenisProduk->affects_income ? 'Ya' : 'Tidak' }}</td>
                            <td class="text-muted small">Slug dikunci untuk logika bisnis.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
