@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Produk</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('produk.edit', $produk) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Informasi Produk</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="180">Nama</th>
                        <td>{{ $produk->nama }}</td>
                    </tr>
                    <tr>
                        <th>Kode</th>
                        <td>{{ $produk->kode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $produk->kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Produk</th>
                        <td>{{ $produk->jenisProduk->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Satuan</th>
                        <td>{{ $produk->satuan }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ ucfirst($produk->status) }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $produk->deskripsi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Stok dan Harga</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Stok Saat Ini</th>
                        <td>{{ $produk->stok }}</td>
                    </tr>
                    <tr>
                        <th>Stok Minimal</th>
                        <td>{{ $produk->stok_minimal }}</td>
                    </tr>
                    <tr>
                        <th>Status Stok</th>
                        <td>{{ ucfirst($produk->stok_status) }}</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td>{{ rupiah($produk->harga_jual) }}</td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>{{ rupiah($produk->harga_beli) }}</td>
                    </tr>
                    <tr>
                        <th>Harga Fleksibel</th>
                        <td>{{ $produk->harga_fleksibel ? 'Ya' : 'Tidak' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Riwayat Pembelian Produk</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Nota</th>
                        <th>Supplier</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produk->pembelians as $pembelian)
                    <tr>
                        <td>{{ $pembelian->tanggal?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $pembelian->nomor_nota }}</td>
                        <td>{{ $pembelian->supplier ?? '-' }}</td>
                        <td>{{ $pembelian->jumlah }}</td>
                        <td>{{ rupiah($pembelian->harga_satuan) }}</td>
                        <td>{{ rupiah($pembelian->total) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Belum ada riwayat pembelian</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
