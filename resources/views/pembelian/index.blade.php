@extends('layouts.app')

@section('title', 'Pembelian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Data Pembelian</h4>
    <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Pembelian
    </a>
</div>

<div class="card mb-4 bg-info text-white">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-1">Total Nilai Pembelian</h6>
            <small>Termasuk pembelian yang otomatis masuk ke pengeluaran.</small>
        </div>
        <h3 class="mb-0">{{ rupiah($totalPembelian) }}</h3>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('pembelian.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Jenis Pembelian</label>
                <select name="jenis_pembelian" class="form-select">
                    <option value="">Semua</option>
                    <option value="produk_jual" @selected(request('jenis_pembelian') === 'produk_jual')>Produk untuk Dijual</option>
                    <option value="bahan_pakai" @selected(request('jenis_pembelian') === 'bahan_pakai')>Bahan Dipakai</option>
                    <option value="alat_inventaris" @selected(request('jenis_pembelian') === 'alat_inventaris')>Alat Inventaris</option>
                    <option value="barang_sewa" @selected(request('jenis_pembelian') === 'barang_sewa')>Barang Disewakan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Nota</th>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Objek</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembelians as $pembelian)
                        <tr>
                            <td>{{ $pembelian->nomor_nota }}</td>
                            <td>{{ $pembelian->tanggal->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ str_replace('_', ' ', $pembelian->jenis_pembelian) }}</span>
                            </td>
                            <td>
                                @if($pembelian->jenis_pembelian === 'alat_inventaris')
                                    <div class="fw-semibold">{{ $pembelian->inventaris->nama_aset ?? '-' }}</div>
                                    <small class="text-muted">{{ $pembelian->inventaris->kode_aset ?? '-' }}</small>
                                @else
                                    <div class="fw-semibold">{{ $pembelian->produk->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $pembelian->produk->jenisProduk->nama ?? '-' }}</small>
                                @endif
                            </td>
                            <td>{{ $pembelian->jumlah }}</td>
                            <td class="fw-semibold">{{ rupiah($pembelian->total) }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('pembelian.show', $pembelian) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('pembelian.edit', $pembelian) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('pembelian.destroy', $pembelian) }}" method="POST" onsubmit="return confirm('Hapus pembelian ini? Pengeluaran terkait juga akan disesuaikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Belum ada data pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $pembelians->links() }}
    </div>
</div>
@endsection
