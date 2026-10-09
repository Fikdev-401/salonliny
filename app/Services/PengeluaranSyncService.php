<?php

namespace App\Services;

use App\Models\Gaji;
use App\Models\Pembelian;
use App\Models\Pengeluaran;

class PengeluaranSyncService
{
    public function syncForPembelian(Pembelian $pembelian): Pengeluaran
    {
        $pengeluaran = $this->findExisting(Pembelian::class, $pembelian->id) ?? new Pengeluaran();

        $pengeluaran->fill([
            'nomor_pengeluaran' => $pengeluaran->exists
                ? $pengeluaran->nomor_pengeluaran
                : 'PGL-' . $pembelian->tanggal->format('Ymd') . '-' . strtoupper(uniqid()),
            'tanggal' => $pembelian->tanggal,
            'kategori' => $this->resolvePembelianKategori($pembelian->jenis_pembelian),
            'nama_pengeluaran' => $this->resolvePembelianNama($pembelian),
            'jumlah' => $pembelian->total,
            'keterangan' => $pembelian->keterangan,
            'user_id' => $pembelian->user_id,
            'sumber_tipe' => Pembelian::class,
            'sumber_id' => $pembelian->id,
            'is_system_generated' => true,
        ]);

        $pengeluaran->save();

        return $pengeluaran;
    }

    public function deleteForPembelian(Pembelian $pembelian): void
    {
        $this->findExisting(Pembelian::class, $pembelian->id)?->delete();
    }

    public function syncForGaji(Gaji $gaji): ?Pengeluaran
    {
        if ($gaji->status !== 'dibayar' || !$gaji->tanggal_bayar) {
            $this->deleteForGaji($gaji);
            return null;
        }

        $pengeluaran = $this->findExisting(Gaji::class, $gaji->id) ?? new Pengeluaran();

        $pengeluaran->fill([
            'nomor_pengeluaran' => $pengeluaran->exists
                ? $pengeluaran->nomor_pengeluaran
                : 'PGL-' . $gaji->tanggal_bayar->format('Ymd') . '-' . strtoupper(uniqid()),
            'tanggal' => $gaji->tanggal_bayar,
            'kategori' => 'Gaji & Insentif',
            'nama_pengeluaran' => 'Pembayaran gaji ' . ($gaji->karyawan->nama ?? 'Karyawan') . ' (' . $gaji->periode . ')',
            'jumlah' => $gaji->total_gaji,
            'keterangan' => $gaji->keterangan,
            'user_id' => $gaji->disetujui_oleh ?: $gaji->diajukan_oleh,
            'sumber_tipe' => Gaji::class,
            'sumber_id' => $gaji->id,
            'is_system_generated' => true,
        ]);

        $pengeluaran->save();

        return $pengeluaran;
    }

    public function deleteForGaji(Gaji $gaji): void
    {
        $this->findExisting(Gaji::class, $gaji->id)?->delete();
    }

    private function findExisting(string $type, int $id): ?Pengeluaran
    {
        return Pengeluaran::where('sumber_tipe', $type)
            ->where('sumber_id', $id)
            ->first();
    }

    private function resolvePembelianKategori(string $jenisPembelian): string
    {
        return match ($jenisPembelian) {
            'bahan_pakai' => 'Pembelian Bahan Pakai',
            'alat_inventaris' => 'Pembelian Alat Inventaris',
            'barang_sewa' => 'Pembelian Barang Sewa',
            default => 'Pembelian Produk Jual',
        };
    }

    private function resolvePembelianNama(Pembelian $pembelian): string
    {
        if ($pembelian->jenis_pembelian === 'alat_inventaris') {
            return 'Pembelian inventaris ' . ($pembelian->inventaris->nama_aset ?? 'Aset');
        }

        return 'Pembelian ' . ($pembelian->produk->nama ?? 'Produk');
    }
}
