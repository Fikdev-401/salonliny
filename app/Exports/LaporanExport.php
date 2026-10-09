<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $type;

    public function __construct($data, $type = 'pendapatan')
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'pendapatan':
                return ['No. Transaksi', 'Tanggal', 'Pelanggan', 'Kasir', 'Metode Pembayaran', 'Total', 'Status'];
            default:
                return ['Data'];
        }
    }

    public function map($row): array
    {
        switch ($this->type) {
            case 'pendapatan':
                return [
                    $row->nomor_transaksi,
                    $row->tanggal_transaksi->format('d/m/Y H:i'),
                    $row->pelanggan->nama ?? '-',
                    $row->kasir->nama ?? '-',
                    ucfirst($row->metode_pembayaran),
                    $row->total,
                    $row->status_pembayaran == 'lunas' ? 'Lunas' : 'Pending',
                ];
            default:
                return [$row];
        }
    }
}
