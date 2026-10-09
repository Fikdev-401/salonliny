@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Pelanggan</h4>
    <div>
        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-person"></i> Informasi Pelanggan
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="120">Nama</td>
                        <td>: <strong>{{ $pelanggan->nama }}</strong></td>
                    </tr>
                    <tr>
                        <td>No. HP</td>
                        <td>: {{ $pelanggan->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tgl Lahir</td>
                        <td>: {{ $pelanggan->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Umur</td>
                        <td>: {{ $pelanggan->umur ? $pelanggan->umur . ' tahun' : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>: {{ $pelanggan->jenis_kelamin == 'L' ? 'Laki-laki' : ($pelanggan->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>: {{ $pelanggan->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>: 
                            @if($pelanggan->status == 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Waktu Registrasi</td>
                        <td>: {{ $pelanggan->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @if($pelanggan->catatan)
                    <tr>
                        <td>Catatan</td>
                        <td>: {{ $pelanggan->catatan }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <i class="bi bi-graph-up"></i> Statistik
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4>{{ $pelanggan->total_kunjungan }}</h4>
                        <small class="text-muted">Total Kunjungan</small>
                    </div>
                    <div class="col-6">
                        <h4>{{ $pelanggan->terakhir_kunjungan?->format('d/m/Y') ?? '-' }}</h4>
                        <small class="text-muted">Kunjungan Terakhir</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Riwayat Transaksi
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Item</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggan->transaksis as $transaksi)
                            <tr>
                                <td>{{ $transaksi->nomor_transaksi }}</td>
                                <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                                <td>{{ $transaksi->details->count() }} item</td>
                                <td class="text-end">{{ rupiah($transaksi->total) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Belum ada riwayat transaksi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
