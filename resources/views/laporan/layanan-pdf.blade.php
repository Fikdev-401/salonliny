<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Layanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Layanan</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Layanan</th>
                <th class="text-center">Jumlah Pesanan</th>
                <th class="text-end">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['layanan']->nama }}</td>
                <td class="text-center">{{ $item['jumlah'] }}</td>
                <td class="text-end">{{ rupiah($item['total_pendapatan']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
