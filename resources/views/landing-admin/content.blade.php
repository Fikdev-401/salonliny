@extends('layouts.app')

@section('title', 'Konten Landing Page')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Konten Landing Page</h1>
        <p class="page-subtitle">Atur hero, about, kontak, dan tautan sosial media yang tampil di halaman utama.</p>
    </div>
    <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Landing Page
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<ul class="nav nav-tabs mb-4" id="contentTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-hero" type="button">Hero</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-about" type="button">Tentang</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kontak" type="button">Kontak & Sosial</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-perusahaan" type="button">Perusahaan</button></li>
</ul>

<form action="{{ route('landing-admin.content.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="tab-content">
        <!-- HERO -->
        <div class="tab-pane fade show active" id="tab-hero">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Bagian Hero</h5>
                    <p class="text-muted small">Tampil pertama kali saat pengunjung membuka landing page.</p>

                    <div class="mb-3">
                        <label class="form-label">Judul Hero</label>
                        <input type="text" name="hero_title" class="form-control @error('hero_title') is-invalid @enderror" value="{{ old('hero_title', $konfigurasi->hero_title) }}" placeholder="cth: LINY Salon">
                        <small class="text-muted">Kata pertama ditampilkan normal, sisanya akan otomatis gold italic.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtitle Hero</label>
                        <textarea name="hero_subtitle" class="form-control" rows="2" placeholder="cth: Hair and Beauty">{{ old('hero_subtitle', $konfigurasi->hero_subtitle) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Latar Hero</label>
                        <input type="file" name="hero_image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        @if($konfigurasi->hero_image_url)
                            <div class="mt-2"><img src="{{ $konfigurasi->hero_image_url }}" style="max-height: 150px; border-radius: 8px;"></div>
                            <small class="text-muted d-block">File saat ini: <code>{{ $konfigurasi->hero_image }}</code></small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template Pesan WhatsApp (Tombol Reservasi)</label>
                        <textarea name="hero_cta_message" class="form-control" rows="3" placeholder="Halo LINY Salon, saya ingin melakukan reservasi. Mohon info jadwal kosongnya. Terima kasih!">{{ old('hero_cta_message', $konfigurasi->hero_cta_message) }}</textarea>
                        <small class="text-muted">Pesan ini akan ter-isi otomatis saat pengunjung klik tombol Reservasi.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ABOUT -->
        <div class="tab-pane fade" id="tab-about">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Bagian Tentang</h5>

                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="about_title" class="form-control" value="{{ old('about_title', $konfigurasi->about_title) }}" placeholder="cth: Cerita di Balik LINY">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teks Tentang</label>
                        <textarea name="about_text" class="form-control" rows="6" placeholder="Ceritakan tentang salon Anda...">{{ old('about_text', $konfigurasi->about_text) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Tentang</label>
                        <input type="file" name="about_image" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                        @if($konfigurasi->about_image_url)
                            <div class="mt-2"><img src="{{ $konfigurasi->about_image_url }}" style="max-height: 200px; border-radius: 8px;"></div>
                            <small class="text-muted d-block">File saat ini: <code>{{ $konfigurasi->about_image }}</code></small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- KONTAK & SOSIAL -->
        <div class="tab-pane fade" id="tab-kontak">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Kontak & Tautan Sosial</h5>

                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp (untuk tombol reservasi)</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $konfigurasi->whatsapp_number) }}" placeholder="cth: 081234567890">
                        <small class="text-muted">Format 08xx atau 62xx. Otomatis dikonversi ke format internasional.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instagram URL</label>
                        <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $konfigurasi->instagram_url) }}" placeholder="https://instagram.com/liny.salon">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">TikTok URL</label>
                        <input type="url" name="tiktok_url" class="form-control" value="{{ old('tiktok_url', $konfigurasi->tiktok_url) }}" placeholder="https://tiktok.com/@liny.salon">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Facebook URL</label>
                        <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', $konfigurasi->facebook_url) }}" placeholder="https://facebook.com/liny.salon">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">YouTube URL</label>
                        <input type="url" name="youtube_url" class="form-control" value="{{ old('youtube_url', $konfigurasi->youtube_url) }}" placeholder="https://youtube.com/@linysalon">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Google Maps Embed URL</label>
                        <input type="text" name="maps_embed_url" class="form-control" value="{{ old('maps_embed_url', $konfigurasi->maps_embed_url) }}" placeholder="https://www.google.com/maps/embed?pb=...">
                        <small class="text-muted">Buka Google Maps → Share → Embed a map → copy URL di dalam atribut <code>src</code> dari iframe.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERUSAHAAN -->
        <div class="tab-pane fade" id="tab-perusahaan">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Data Perusahaan (juga tampil di footer & kontak)</h5>

                    <div class="mb-3">
                        <label class="form-label">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan', $konfigurasi->nama_perusahaan) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $konfigurasi->alamat) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $konfigurasi->no_telepon) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $konfigurasi->email) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Buka</label>
                            <input type="text" name="jam_buka" class="form-control" value="{{ old('jam_buka', $konfigurasi->jam_buka) }}" placeholder="cth: 09:00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Tutup</label>
                            <input type="text" name="jam_tutup" class="form-control" value="{{ old('jam_tutup', $konfigurasi->jam_tutup) }}" placeholder="cth: 21:00">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-5">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
        <a href="{{ route('landing-admin.gallery.index') }}" class="btn btn-outline-secondary ms-2">Kelola Galeri</a>
        <a href="{{ route('landing-admin.videos.index') }}" class="btn btn-outline-secondary ms-2">Kelola Video</a>
        <a href="{{ route('landing-admin.testimonis.index') }}" class="btn btn-outline-secondary ms-2">Kelola Testimoni</a>
    </div>
</form>

@push('scripts')
<script>
    // Keep active tab on validation error
    @if($errors->any())
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            const tab = firstError.closest('.tab-pane');
            if (tab) {
                const trigger = document.querySelector(`[data-bs-target="#${tab.id}"]`);
                if (trigger) new bootstrap.Tab(trigger).show();
            }
        }
    @endif
</script>
@endpush
@endsection
