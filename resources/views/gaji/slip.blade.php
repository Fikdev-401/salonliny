<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f3f3f3; text-align: left; width: 35%; }
        .total th, .total td { font-weight: bold; background: #f9f9f9; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Slip Gaji & Insentif</h2>
        <div class="muted">Periode: {{ $gaji->periode }}</div>
        <div class="muted">Dicetak: {{ now()->format('d/m/Y H:i') }} WITA</div>
    </div>

    <table>
        <tr><th>Nama Karyawan</th><td>{{ $gaji->karyawan->nama ?? '-' }}</td></tr>
        <tr><th>Gaji Pokok Snapshot</th><td>{{ rupiah($gaji->gaji_pokok) }}</td></tr>
        <tr><th>Total Insentif Snapshot</th><td>{{ rupiah($gaji->total_insentif) }}</td></tr>
        <tr><th>Bonus</th><td>{{ rupiah($gaji->bonus) }}</td></tr>
        <tr><th>THR</th><td>{{ rupiah($gaji->thr) }}</td></tr>
        <tr><th>Potongan</th><td>{{ rupiah($gaji->potongan) }}</td></tr>
        <tr class="total"><th>Total Gaji</th><td>{{ rupiah($gaji->total_gaji) }}</td></tr>
        <tr>
            <th>Rekap Absensi</th>
            <td>Hadir {{ $gaji->jumlah_hadir }}, Izin {{ $gaji->jumlah_izin }}, Sakit {{ $gaji->jumlah_sakit }}, Alpha {{ $gaji->jumlah_alpha }}</td>
        </tr>
        <tr><th>Tanggal Bayar</th><td>{{ $gaji->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td></tr>
    </table>
</body>
</html>
