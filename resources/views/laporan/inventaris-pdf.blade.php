<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Inventaris</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f2f2f2; text-align: left; }
    </style>
</head>
<body>
    <h2>Laporan Inventaris</h2>
    <p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
    <p>Total data aset: {{ number_format($totalItem, 0, ',', '.') }} | Total unit: {{ number_format($totalUnit, 0, ',', '.') }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode Aset</th>
                <th>Nama Aset</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Tanggal Input</th>
                <th>Status</th>
                <th>Cabang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventaris as $item)
                <tr>
                    <td>{{ $item->kode_aset }}</td>
                    <td>{{ $item->nama_aset }}</td>
                    <td>{{ $item->jenis_aset }}</td>
                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>{{ $item->tanggal_input->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                    <td>{{ $item->cabang->nama ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
