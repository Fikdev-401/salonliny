@extends('layouts.app')

@section('title', 'Detail Layanan')

@section('content')
<div class="page-header">
    <div>
        <h4 class="page-title">Detail Layanan</h4>
        <p class="page-subtitle">Ringkasan lengkap konfigurasi layanan yang dipakai dalam transaksi.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('layanan.edit', $layanan) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('layanan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-tile">
        <span class="summary-label">Harga Default</span>
        <div class="summary-value">{{ rupiah($layanan->harga_default) }}</div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Durasi</span>
        <div class="summary-value-sm">{{ $layanan->durasi_menit ? $layanan->durasi_menit . ' menit' : 'Tidak ditentukan' }}</div>
    </div>
    <div class="summary-tile">
        <span class="summary-label">Status</span>
        <div class="summary-value-sm">
            @if($layanan->status === 'aktif')
                <span class="status-dot text-success">Aktif</span>
            @else
                <span class="status-dot text-secondary">Nonaktif</span>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="section-heading">
            <div>
                <h5 class="section-title">Informasi Layanan</h5>
                <p class="section-caption">Data ini dipakai sebagai referensi utama saat kasir membuat transaksi.</p>
            </div>
        </div>

        <div class="soft-panel">
            <div class="detail-list">
                <div class="detail-row">
                    <div class="detail-label">Nama Layanan</div>
                    <div class="detail-value fw-semibold">{{ $layanan->nama }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Kategori</div>
                    <div class="detail-value">{{ $layanan->kategori ?? '-' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Harga Fleksibel</div>
                    <div class="detail-value">{{ $layanan->harga_fleksibel ? 'Ya, harga bisa diubah saat transaksi' : 'Tidak, selalu memakai harga default' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Deskripsi</div>
                    <div class="detail-value">{{ $layanan->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
