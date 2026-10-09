@extends('layouts.app')

@section('title', 'Detail Pengeluaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Pengeluaran</h4>
    <a href="{{ route('pengeluaran.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span>{{ $pengeluaran->nomor_pengeluaran }}</span>
                <span>{{ $pengeluaran->tanggal->format('d/m/Y') }}</span>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><th width="220">Kategori</th><td>{{ $pengeluaran->kategori }}</td></tr>
                    <tr><th>Nama Pengeluaran</th><td>{{ $pengeluaran->nama_pengeluaran }}</td></tr>
                    <tr><th>Jumlah</th><td class="fw-semibold text-danger">{{ rupiah($pengeluaran->jumlah) }}</td></tr>
                    <tr><th>Sumber Data</th><td>{{ $pengeluaran->is_system_generated ? 'Otomatis' : 'Manual' }}</td></tr>
                    <tr><th>Keterangan</th><td>{{ $pengeluaran->keterangan ?: '-' }}</td></tr>
                    <tr><th>Dicatat Oleh</th><td>{{ $pengeluaran->user->nama ?? '-' }}</td></tr>
                    <tr><th>Tanggal Input</th><td>{{ $pengeluaran->created_at->format('d/m/Y H:i') }}</td></tr>
                </table>

                @if($pengeluaran->is_system_generated && $sourceRoute)
                    <div class="alert alert-warning mb-0">
                        Pengeluaran ini dibuat otomatis dari data sumber.
                        <a href="{{ $sourceRoute }}" class="alert-link">Buka sumbernya</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($pengeluaran->bukti_nota)
            <div class="card">
                <div class="card-header">Bukti Nota</div>
                <div class="card-body">
                    @if(str_ends_with($pengeluaran->bukti_nota, '.pdf'))
                        <a href="{{ route('pengeluaran.bukti', $pengeluaran) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-file-pdf"></i> Lihat PDF
                        </a>
                    @else
                        <a href="{{ route('pengeluaran.bukti', $pengeluaran) }}" target="_blank">
                            <img src="{{ route('pengeluaran.bukti', $pengeluaran) }}" alt="Bukti Nota" class="img-fluid img-thumbnail">
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
