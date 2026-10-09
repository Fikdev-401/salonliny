@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Transaksi</h4>
    <div class="d-flex gap-2">
        @if($whatsappShareUrl)
            <a href="{{ $whatsappShareUrl }}" class="btn btn-success" target="_blank" rel="noopener">
                <i class="bi bi-whatsapp"></i> Share WA
            </a>
        @else
            <button type="button" class="btn btn-success" disabled title="Nomor WhatsApp pelanggan belum tersedia">
                <i class="bi bi-whatsapp"></i> Share WA
            </button>
        @endif
        <a href="{{ route('transaksi.cetak', $transaksi) }}" class="btn btn-secondary" target="_blank">
            <i class="bi bi-printer"></i> Cetak Struk
        </a>
        @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('transaksi.edit', $transaksi) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        @endif
        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>{{ $transaksi->nomor_transaksi }}</span>
                <span>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }} WITA</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small">Pelanggan</div>
                        <div class="fw-semibold">{{ $transaksi->pelanggan->nama ?? '-' }}</div>
                        <div>{{ $transaksi->pelanggan->no_hp ?? '-' }}</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="text-muted small">Kasir</div>
                        <div class="fw-semibold">{{ $transaksi->kasir->nama ?? '-' }}</div>
                        <span class="badge {{ $transaksi->status_pembayaran === 'lunas' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ strtoupper($transaksi->status_pembayaran) }}
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksi->details as $detail)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $detail->nama_item }}</div>
                                        <div class="small text-muted">{{ ucfirst($detail->tipe) }}</div>
                                        @if($detail->tipe === 'layanan')
                                            <div class="small mt-1">
                                                @if($detail->karyawanUtama)
                                                    <div>Utama: {{ $detail->karyawanUtama->nama }} | Insentif {{ rupiah($detail->insentif_utama) }}</div>
                                                @endif
                                                @if($detail->karyawanPendukung1)
                                                    <div>Pendukung 1: {{ $detail->karyawanPendukung1->nama }} | Insentif {{ rupiah($detail->insentif_pendukung_1) }}</div>
                                                @endif
                                                @if($detail->karyawanPendukung2)
                                                    <div>Pendukung 2: {{ $detail->karyawanPendukung2->nama }} | Insentif {{ rupiah($detail->insentif_pendukung_2) }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $detail->jumlah }} {{ $detail->satuan }}</td>
                                    <td>{{ rupiah($detail->harga_satuan) }}</td>
                                    <td>{{ rupiah($detail->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal</th>
                                <th>{{ rupiah($transaksi->subtotal) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Diskon</th>
                                <th>{{ rupiah($transaksi->diskon) }}</th>
                            </tr>
                            <tr class="table-primary">
                                <th colspan="3" class="text-end">Total</th>
                                <th>{{ rupiah($transaksi->total) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($transaksi->catatan)
                    <div class="alert alert-light mb-0">
                        <div class="fw-semibold mb-1">Catatan</div>
                        <div>{{ $transaksi->catatan }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Pembayaran</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td>Total Insentif</td>
                        <td class="text-end">{{ rupiah($transaksi->total_insentif) }}</td>
                    </tr>
                    <tr>
                        <td>Metode</td>
                        <td class="text-end">{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td class="text-end">{{ rupiah($transaksi->total) }}</td>
                    </tr>
                    <tr>
                        <td>Bayar</td>
                        <td class="text-end">{{ rupiah($transaksi->bayar) }}</td>
                    </tr>
                    <tr>
                        <td>Kembalian</td>
                        <td class="text-end fw-semibold">{{ rupiah($transaksi->kembalian) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Ringkas Pelanggan</div>
            <div class="card-body">
                <div>Total kunjungan: <span class="fw-semibold">{{ $transaksi->pelanggan->total_kunjungan ?? 0 }}x</span></div>
                <div class="mt-2">Terakhir kunjungan: <span class="fw-semibold">{{ $transaksi->pelanggan->terakhir_kunjungan?->format('d/m/Y') ?? '-' }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
