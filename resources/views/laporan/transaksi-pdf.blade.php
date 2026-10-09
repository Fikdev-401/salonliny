<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1cc88a; color: white; }
        .text-end { text-align: right; }
        .summary { margin: 20px 0; padding: 15px; background: #f8f9fa; }
        .summary-value { font-size: 18px; font-weight: bold; color: #1cc88a; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
        .badge-success { background: #1cc88a; color: white; }
        .badge-warning { background: #f6c23e; color: white; }
        .badge-danger { background: #e74a3b; color: white; }
        .workers { font-size: 10px; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Transaksi</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-label">Total Pendapatan</div>
        <div class="summary-value">{{ rupiah($totalPendapatan) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th>Karyawan</th>
                <th>Metode</th>
                <th class="text-end">Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $item)
            @php
                $workers = [];
                foreach($item->details as $detail) {
                    if($detail->karyawanUtama) {
                        $workers[] = 'Utama: ' . $detail->karyawanUtama->nama;
                    }
                    if($detail->karyawanPendukung1) {
                        $workers[] = 'Pendukung: ' . $detail->karyawanPendukung1->nama;
                    }
                    if($detail->karyawanPendukung2) {
                        $workers[] = 'Pendukung: ' . $detail->karyawanPendukung2->nama;
                    }
                }
                $workers = array_unique($workers);
            @endphp
            <tr>
                <td>{{ $item->nomor_transaksi }}</td>
                <td>{{ $item->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                <td>{{ $item->pelanggan->nama ?? '-' }}</td>
                <td>{{ $item->kasir->nama ?? '-' }}</td>
                <td class="workers">
                    @if(count($workers) > 0)
                        @foreach($workers as $worker)
                            {{ $worker }}@if(!$loop->last)<br>@endif
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>{{ ucfirst($item->metode_pembayaran) }}</td>
                <td class="text-end">{{ rupiah($item->total) }}</td>
                <td>
                    @if($item->status_pembayaran == 'lunas')
                        <span class="badge badge-success">Lunas</span>
                    @elseif($item->status_pembayaran == 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @else
                        <span class="badge badge-danger">Batal</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 10px;">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
