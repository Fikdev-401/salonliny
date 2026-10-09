<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Gaji</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Gaji</h2>
        <p>Periode: {{ $periode }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Karyawan</th>
                <th class="text-end">Gaji Pokok</th>
                <th class="text-end">Insentif</th>
                <th class="text-end">Bonus</th>
                <th class="text-end">Potongan</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gajis as $item)
            <tr>
                <td>{{ $item->karyawan->nama }}</td>
                <td class="text-end">{{ rupiah($item->gaji_pokok) }}</td>
                <td class="text-end">{{ rupiah($item->total_insentif) }}</td>
                <td class="text-end">{{ rupiah($item->bonus) }}</td>
                <td class="text-end">{{ rupiah($item->potongan) }}</td>
                <td class="text-end">{{ rupiah($item->total_gaji) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-end"><strong>Total</strong></td>
                <td class="text-end"><strong>{{ rupiah($gajis->sum('gaji_pokok')) }}</strong></td>
                <td class="text-end"><strong>{{ rupiah($gajis->sum('total_insentif')) }}</strong></td>
                <td class="text-end"><strong>{{ rupiah($gajis->sum('bonus')) }}</strong></td>
                <td class="text-end"><strong>{{ rupiah($gajis->sum('potongan')) }}</strong></td>
                <td class="text-end"><strong>{{ rupiah($totalGaji) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
