<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4e73df; color: white; }
        .text-end { text-align: right; }
        .summary { margin: 20px 0; padding: 15px; background: #f8f9fa; }
        .summary-item { display: inline-block; margin-right: 50px; }
        .summary-label { font-size: 11px; color: #666; }
        .summary-value { font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pendapatan</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Pendapatan</div>
            <div class="summary-value">{{ rupiah($totalPendapatan) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Transaksi</div>
            <div class="summary-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
        </div>
    </div>

    <h4>Ringkasan Harian</h4>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Transaksi</th>
                <th class="text-end">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendapatanPerTanggal as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td>{{ $item->jumlah_transaksi }}</td>
                <td class="text-end">{{ rupiah($item->total_pendapatan) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h4 style="margin-top: 30px;">Detail Transaksi</h4>
    <table>
        <thead>
            <tr>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailTransaksi as $transaksi)
            <tr>
                <td>{{ $transaksi->nomor_transaksi }}</td>
                <td>{{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }}</td>
                <td>{{ $transaksi->pelanggan->nama ?? '-' }}</td>
                <td>{{ $transaksi->kasir->nama ?? '-' }}</td>
                <td>{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                <td class="text-end">{{ rupiah($transaksi->total) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data transaksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 10px;">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
