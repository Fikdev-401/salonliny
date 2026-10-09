<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
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
        .summary-item { margin-bottom: 6px; }
        .summary-label { color: #666; }
        .summary-value { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Laba Rugi</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item"><span class="summary-label">Total Pendapatan:</span> <span class="summary-value">{{ rupiah($pendapatanTransaksi) }}</span></div>
        <div class="summary-item"><span class="summary-label">Total Pengeluaran:</span> <span class="summary-value">{{ rupiah($totalPengeluaran) }}</span></div>
        <div class="summary-item"><span class="summary-label">Laba / Rugi:</span> <span class="summary-value">{{ rupiah($labaRugi) }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th>Tipe</th>
                <th class="text-end">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rincian as $item)
            <tr>
                <td>{{ $item['nama'] }}</td>
                <td>{{ ucfirst($item['tipe']) }}</td>
                <td class="text-end">{{ rupiah($item['jumlah']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right; font-size: 10px;">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
