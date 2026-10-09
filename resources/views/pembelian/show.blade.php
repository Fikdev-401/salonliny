@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Pembelian</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('pembelian.edit', $pembelian) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Informasi Pembelian</div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th width="220">No. Nota</th>
                        <td>{{ $pembelian->nomor_nota }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $pembelian->tanggal->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Pembelian</th>
                        <td>{{ str_replace('_', ' ', $pembelian->jenis_pembelian) }}</td>
                    </tr>
                    <tr>
                        <th>Objek Pembelian</th>
                        <td>
                            @if($pembelian->jenis_pembelian === 'alat_inventaris')
                                {{ $pembelian->inventaris->nama_aset ?? '-' }} ({{ $pembelian->inventaris->kode_aset ?? '-' }})
                            @else
                                {{ $pembelian->produk->nama ?? '-' }} - {{ $pembelian->produk->jenisProduk->nama ?? '-' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jumlah</th>
                        <td>{{ $pembelian->jumlah }}</td>
                    </tr>
                    <tr>
                        <th>Harga Satuan</th>
                        <td>{{ rupiah($pembelian->harga_satuan) }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td class="fw-semibold">{{ rupiah($pembelian->total) }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>{{ $pembelian->supplier ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. HP Supplier</th>
                        <td>{{ $pembelian->no_hp_supplier ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dicatat Oleh</th>
                        <td>{{ $pembelian->user->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $pembelian->keterangan ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Sinkron Pengeluaran</div>
            <div class="card-body">
                @if($pembelian->pengeluaran)
                    <div class="small text-muted">Nomor Pengeluaran</div>
                    <div class="fw-semibold mb-2">{{ $pembelian->pengeluaran->nomor_pengeluaran }}</div>
                    <div class="small text-muted">Jumlah</div>
                    <div class="fw-semibold mb-2">{{ rupiah($pembelian->pengeluaran->jumlah) }}</div>
                    <a href="{{ route('pengeluaran.show', $pembelian->pengeluaran) }}" class="btn btn-sm btn-outline-primary">
                        Lihat Pengeluaran
                    </a>
                @else
                    <div class="text-muted">Belum ada pengeluaran sinkron.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
