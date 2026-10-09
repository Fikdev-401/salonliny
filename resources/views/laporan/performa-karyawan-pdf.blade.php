<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Performa Karyawan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; }
        h2, p { margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        th { background: #f2f2f2; text-align: left; }
        .text-end { text-align: right; }
        .section { margin-top: 16px; }
    </style>
</head>
<body>
    <h2>Laporan Performa Karyawan</h2>
    <p>Bulan: {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}</p>
    <p>Total Insentif: {{ rupiah(collect($laporan)->sum('total_insentif')) }}</p>

    <table>
        <thead>
            <tr>
                <th>Karyawan</th>
                <th>Jabatan</th>
                <th>Layanan Utama</th>
                <th>Layanan Pendukung</th>
                <th>Total Layanan</th>
                <th>Total Insentif</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $item)
                <tr>
                    <td>{{ $item['karyawan']->nama }}</td>
                    <td>{{ $item['karyawan']->jabatan }}</td>
                    <td>{{ $item['layanan_utama'] }}</td>
                    <td>{{ $item['layanan_pendukung'] }}</td>
                    <td>{{ $item['layanan_utama'] + $item['layanan_pendukung'] }}</td>
                    <td>{{ rupiah($item['total_insentif']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @foreach($laporan as $item)
        @if($item['details']->isNotEmpty())
            <div class="section">
                <strong>{{ $item['karyawan']->nama }}</strong> - {{ rupiah($item['total_insentif']) }}
                <table>
                    <thead>
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
                        @foreach($item['details'] as $detail)
                            <tr>
                                <td>{{ $detail->transaksi->tanggal_transaksi?->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>{{ $detail->transaksi->nomor_transaksi ?? '-' }}</td>
                                <td>{{ $detail->transaksi->pelanggan->nama ?? '-' }}</td>
                                <td>{{ $detail->nama_item }}</td>
                                <td>{{ $detail->role }}</td>
                                <td>{{ rupiah($detail->insentif) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach
</body>
</html>
