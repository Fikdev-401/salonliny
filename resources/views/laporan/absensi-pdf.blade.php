<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Absensi</h2>
        <p>Bulan: {{ \Carbon\Carbon::parse($bulan . '-01')->format('F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Karyawan</th>
                <th class="text-center">Hadir</th>
                <th class="text-center">Izin</th>
                <th class="text-center">Sakit</th>
                <th class="text-center">Alpha</th>
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekap as $item)
            <tr>
                <td>{{ $item['karyawan']->nama }}</td>
                <td class="text-center">{{ $item['hadir'] }}</td>
                <td class="text-center">{{ $item['izin'] }}</td>
                <td class="text-center">{{ $item['sakit'] }}</td>
                <td class="text-center">{{ $item['alpha'] }}</td>
                <td class="text-center"><strong>{{ $item['total'] }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
