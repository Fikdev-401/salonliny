@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Daftar Transaksi</h4>
    <a href="{{ route('transaksi.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Transaksi Baru
    </a>
</div>

<div class="card mb-4 bg-success text-white">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-1">Total Pemasukan Transaksi Lunas</h6>
            <small>{{ request('tanggal_mulai', 'Semua tanggal') }} s/d {{ request('tanggal_selesai', 'sekarang') }}</small>
        </div>
        <h3 class="mb-0">{{ rupiah($totalPemasukan) }}</h3>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('transaksi.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status Pembayaran</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="lunas" @selected(request('status') === 'lunas')>Lunas</option>
                    <option value="belum_lunas" @selected(request('status') === 'belum_lunas')>Pending</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-secondary"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Item</th>
                        <th>Insentif</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $transaksi)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $transaksi->nomor_transaksi }}</div>
                                <small class="text-muted">{{ ucfirst($transaksi->metode_pembayaran) }}</small>
                            </td>
                            <td>{{ $transaksi->tanggal_transaksi?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>{{ $transaksi->pelanggan->nama ?? '-' }}</td>
                            <td>
                                <div class="small">
                                    @foreach($transaksi->details as $detail)
                                        <div>{{ $detail->nama_item }} x{{ $detail->jumlah }}</div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="fw-semibold">{{ rupiah($transaksi->total_insentif) }}</td>
                            <td class="fw-semibold">{{ rupiah($transaksi->total) }}</td>
                            <td>
                                <span class="badge {{ $transaksi->status_pembayaran === 'lunas' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ strtoupper($transaksi->status_pembayaran) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('transaksi.show', $transaksi) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('transaksi.struk', $transaksi) }}" class="btn btn-sm btn-secondary" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    @if(auth()->user()->isSuperAdmin())
                                        <a href="{{ route('transaksi.edit', $transaksi) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('transaksi.destroy', $transaksi) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini? Stok produk akan dikembalikan dan histori pelanggan disesuaikan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $transaksis->links() }}
    </div>
</div>
@endsection
