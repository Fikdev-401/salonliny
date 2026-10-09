@extends('layouts.app')

@section('title', 'Detail Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Inventaris</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('inventaris.edit', $inventaris) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Informasi Aset</div>
            <div class="card-body">
                <table class="table">
                    <tr><th width="220">Kode Aset</th><td>{{ $inventaris->kode_aset }}</td></tr>
                    <tr><th>Nama Aset</th><td>{{ $inventaris->nama_aset }}</td></tr>
                    <tr><th>Jenis Aset</th><td>{{ $inventaris->jenis_aset }}</td></tr>
                    <tr><th>Jumlah</th><td>{{ number_format($inventaris->jumlah, 0, ',', '.') }}</td></tr>
                    <tr><th>Satuan</th><td>{{ $inventaris->satuan }}</td></tr>
                    <tr><th>Tanggal Input</th><td>{{ $inventaris->tanggal_input->format('d/m/Y') }}</td></tr>
                    <tr><th>Status</th><td>{{ ucfirst($inventaris->status) }}</td></tr>
                    <tr><th>Keterangan</th><td>{{ $inventaris->keterangan ?: '-' }}</td></tr>
                    <tr><th>Cabang</th><td>{{ $inventaris->cabang->nama ?? '-' }}</td></tr>
                    <tr><th>Dibuat Oleh</th><td>{{ $inventaris->creator->nama ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Riwayat Pembelian Terkait</div>
            <div class="card-body">
                @forelse($inventaris->pembelians as $pembelian)
                    <div class="border rounded p-2 mb-2">
                        <div class="fw-semibold">{{ $pembelian->nomor_nota }}</div>
                        <div class="small text-muted">{{ $pembelian->tanggal->format('d/m/Y') }}</div>
                        <div>{{ rupiah($pembelian->total) }}</div>
                    </div>
                @empty
                    <div class="text-muted">Belum ada pembelian terkait.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
