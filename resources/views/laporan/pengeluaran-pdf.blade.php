<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pengeluaran</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Nama Pengeluaran</th>
                <th class="text-end">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengeluaran as $item)
            <tr>
                <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->nama_pengeluaran }}</td>
                <td class="text-end">{{ rupiah($item->jumlah) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-end"><strong>Total Pengeluaran</strong></td>
                <td class="text-end"><strong>{{ rupiah($totalPengeluaran) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
