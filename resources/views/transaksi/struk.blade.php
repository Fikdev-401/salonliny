<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $transaksi->nomor_transaksi }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.45;
            width: 78mm;
            margin: 0 auto;
            padding: 5mm;
        }
        .center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; vertical-align: top; }
        .text-right { text-align: right; }
        .small { font-size: 10px; }
        .strong { font-weight: bold; }
    </style>
</head>
<body>
    <div class="center">
        <div class="strong">SALON & BARBER</div>
        <div class="small">{{ \App\Models\Konfigurasi::getInstance()->alamat }}</div>
        <div class="small">{{ \App\Models\Konfigurasi::getInstance()->no_telepon }}</div>
    </div>

    <div class="divider"></div>

    <div>No: {{ $transaksi->nomor_transaksi }}</div>
    <div>Tgl: {{ $transaksi->tanggal_transaksi->format('d/m/Y H:i') }} WITA</div>
    <div>Pelanggan: {{ $transaksi->pelanggan->nama ?? '-' }}</div>
    <div>Kasir: {{ $transaksi->kasir->nama ?? '-' }}</div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->details as $detail)
                <tr>
                    <td>
                        {{ $detail->nama_item }}
                        @if($detail->tipe === 'layanan' && $detail->karyawanUtama)
                            <div class="small">{{ $detail->karyawanUtama->nama }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $detail->jumlah }}</td>
                    <td class="text-right">{{ rupiah($detail->harga_satuan, false) }}</td>
                    <td class="text-right">{{ rupiah($detail->total, false) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ rupiah($transaksi->subtotal) }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="text-right">{{ rupiah($transaksi->diskon) }}</td>
        </tr>
        <tr>
            <td class="strong">Total</td>
            <td class="text-right strong">{{ rupiah($transaksi->total) }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ ucfirst($transaksi->metode_pembayaran) }})</td>
            <td class="text-right">{{ rupiah($transaksi->bayar) }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="text-right">{{ rupiah($transaksi->kembalian) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="center small">
        <div>Terima kasih atas kunjungan Anda.</div>
        <div>Simpan struk ini sebagai bukti pembayaran.</div>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
