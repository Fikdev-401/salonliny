@extends('layouts.app')

@section('title', 'Detail Gaji')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Gaji</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('gaji.slip', $gaji) }}" class="btn btn-secondary" target="_blank">
            <i class="bi bi-file-pdf"></i> Slip PDF
        </a>
        <a href="{{ route('gaji.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Informasi Gaji</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>Karyawan</th><td>{{ $gaji->karyawan->nama ?? '-' }}</td></tr>
                    <tr><th>Periode</th><td>{{ $gaji->periode }}</td></tr>
                    <tr><th>Status</th><td>{{ ucfirst($gaji->status) }}</td></tr>
                    <tr><th>Gaji Pokok</th><td>{{ rupiah($gaji->gaji_pokok) }}</td></tr>
                    <tr><th>Total Insentif</th><td>{{ rupiah($gaji->total_insentif) }}</td></tr>
                    <tr><th>Bonus</th><td>{{ rupiah($gaji->bonus) }}</td></tr>
                    <tr><th>THR</th><td>{{ rupiah($gaji->thr) }}</td></tr>
                    <tr><th>Potongan</th><td>{{ rupiah($gaji->potongan) }}</td></tr>
                    <tr class="fw-semibold"><th>Total Gaji</th><td>{{ rupiah($gaji->total_gaji) }}</td></tr>
                    <tr><th>Tanggal Bayar</th><td>{{ $gaji->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Rekap Absensi & Sinkron Pengeluaran</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>Hadir</th><td>{{ $gaji->jumlah_hadir }}</td></tr>
                    <tr><th>Izin</th><td>{{ $gaji->jumlah_izin }}</td></tr>
                    <tr><th>Sakit</th><td>{{ $gaji->jumlah_sakit }}</td></tr>
                    <tr><th>Alpha</th><td>{{ $gaji->jumlah_alpha }}</td></tr>
                    <tr><th>Diajukan Oleh</th><td>{{ $gaji->diajukanOleh->nama ?? '-' }}</td></tr>
                    <tr><th>Disetujui Oleh</th><td>{{ $gaji->disetujuiOleh->nama ?? '-' }}</td></tr>
                    <tr><th>Keterangan</th><td>{{ $gaji->keterangan ?: '-' }}</td></tr>
                </table>

                @if($gaji->pengeluaran)
                    <div class="alert alert-success mb-0">
                        Pengeluaran otomatis: <a href="{{ route('pengeluaran.show', $gaji->pengeluaran) }}" class="alert-link">{{ $gaji->pengeluaran->nomor_pengeluaran }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Sumber Insentif Per Transaksi</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Item</th>
                        <th>Peran</th>
                        <th>Insentif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiDetails as $detail)
                        @php
                            $peran = '-';
                            $insentif = 0;
                            if ($detail->karyawan_utama_id == $gaji->karyawan_id) {
                                $peran = 'Utama';
                                $insentif = $detail->insentif_utama;
                            } elseif ($detail->karyawan_pendukung_1_id == $gaji->karyawan_id) {
                                $peran = 'Pendukung 1';
                                $insentif = $detail->insentif_pendukung_1;
                            } elseif ($detail->karyawan_pendukung_2_id == $gaji->karyawan_id) {
                                $peran = 'Pendukung 2';
                                $insentif = $detail->insentif_pendukung_2;
                            }
                        @endphp
                        <tr>
                            <td>{{ $detail->transaksi->tanggal_transaksi?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>{{ $detail->transaksi->nomor_transaksi ?? '-' }}</td>
                            <td>{{ $detail->transaksi->pelanggan->nama ?? '-' }}</td>
                            <td>{{ $detail->nama_item }}</td>
                            <td>{{ $peran }}</td>
                            <td>{{ rupiah($insentif) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada transaksi sumber insentif pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
