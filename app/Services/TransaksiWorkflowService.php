<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Layanan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiWorkflowService
{
    public function __construct(
        private readonly GajiDraftSyncService $gajiDraftSyncService
    ) {
    }

    public function create(array $payload, User $actor): Transaksi
    {
        return DB::transaction(function () use ($payload, $actor) {
            $normalizedItems = $this->normalizeItems($payload['items'], $actor);
            $amounts = $this->resolveAmounts($normalizedItems, $payload);

            $transaksi = Transaksi::create([
                'nomor_transaksi' => $this->generateNomorTransaksi($payload['tanggal_transaksi']),
                'tanggal_transaksi' => $payload['tanggal_transaksi'],
                'pelanggan_id' => $payload['pelanggan_id'],
                'kasir_id' => $actor->id,
                'cabang_id' => $actor->cabang_id,
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
                'bayar' => $amounts['bayar'],
                'kembalian' => $amounts['kembalian'],
                'metode_pembayaran' => $payload['metode_pembayaran'],
                'status_pembayaran' => $amounts['status_pembayaran'],
                'status' => $amounts['status'],
                'catatan' => $payload['catatan'] ?? null,
            ]);

            $this->persistDetails($transaksi, $normalizedItems, $actor);
            $this->syncPelangganVisitStats((int) $transaksi->pelanggan_id, $actor);
            $this->syncDraftGajis($this->buildDraftSyncTargets($payload['tanggal_transaksi'], $normalizedItems));

            return $transaksi->load([
                'pelanggan',
                'kasir',
                'details.layanan',
                'details.produk',
                'details.karyawanUtama',
                'details.karyawanPendukung1',
                'details.karyawanPendukung2',
            ]);
        });
    }

    public function update(Transaksi $transaksi, array $payload, User $actor): Transaksi
    {
        return DB::transaction(function () use ($transaksi, $payload, $actor) {
            $oldPelangganId = (int) $transaksi->pelanggan_id;
            $transaksi->loadMissing('details');
            $oldTargets = $this->buildDraftSyncTargets($transaksi->tanggal_transaksi, $transaksi->details->toArray());

            $this->restoreProductStock($transaksi, $actor);
            $transaksi->details()->delete();

            $normalizedItems = $this->normalizeItems($payload['items'], $actor);
            $amounts = $this->resolveAmounts($normalizedItems, $payload);

            $transaksi->update([
                'tanggal_transaksi' => $payload['tanggal_transaksi'],
                'pelanggan_id' => $payload['pelanggan_id'],
                'subtotal' => $amounts['subtotal'],
                'diskon' => $amounts['diskon'],
                'total' => $amounts['total'],
                'bayar' => $amounts['bayar'],
                'kembalian' => $amounts['kembalian'],
                'metode_pembayaran' => $payload['metode_pembayaran'],
                'status_pembayaran' => $amounts['status_pembayaran'],
                'status' => $amounts['status'],
                'catatan' => $payload['catatan'] ?? null,
            ]);

            $this->persistDetails($transaksi, $normalizedItems, $actor);

            $this->syncPelangganVisitStats($oldPelangganId, $actor);
            if ($oldPelangganId !== (int) $transaksi->pelanggan_id) {
                $this->syncPelangganVisitStats((int) $transaksi->pelanggan_id, $actor);
            }
            $this->syncDraftGajis(array_merge(
                $oldTargets,
                $this->buildDraftSyncTargets($payload['tanggal_transaksi'], $normalizedItems)
            ));

            return $transaksi->fresh([
                'pelanggan',
                'kasir',
                'details.layanan',
                'details.produk',
                'details.karyawanUtama',
                'details.karyawanPendukung1',
                'details.karyawanPendukung2',
            ]);
        });
    }

    public function delete(Transaksi $transaksi): void
    {
        DB::transaction(function () use ($transaksi) {
            $pelangganId = (int) $transaksi->pelanggan_id;
            $transaksi->loadMissing('details');
            $targets = $this->buildDraftSyncTargets($transaksi->tanggal_transaksi, $transaksi->details->toArray());

            $this->restoreProductStock($transaksi);
            $transaksi->delete();

            $this->syncPelangganVisitStats($pelangganId);
            $this->syncDraftGajis($targets);
        });
    }

    public function normalizeItems(array $items, ?User $actor = null): array
    {
        $normalizedItems = [];

        foreach ($items as $index => $item) {
            $tipe = $item['tipe'] ?? null;

            if ($tipe === 'layanan') {
                $layanan = Layanan::query()
                    ->where('id', $item['item_id'])
                    ->where('status', 'aktif')
                    ->first();

                if (!$layanan) {
                    throw ValidationException::withMessages([
                        "items.{$index}.item_id" => 'Layanan tidak valid atau tidak aktif.',
                    ]);
                }

                $this->validateKaryawanAssignments($item, $index, $actor);

                $hargaSatuan = $layanan->harga_fleksibel
                    ? (float) ($item['harga_satuan'] ?? 0)
                    : (float) $layanan->harga_default;

                $jumlah = (int) ($item['jumlah'] ?? 0);

                $normalizedItems[] = [
                    'tipe' => 'layanan',
                    'layanan_id' => $layanan->id,
                    'produk_id' => null,
                    'nama_item' => $layanan->nama,
                    'jumlah' => $jumlah,
                    'satuan' => 'x',
                    'harga_satuan' => $hargaSatuan,
                    'total' => $hargaSatuan * $jumlah,
                    'karyawan_utama_id' => $item['karyawan_utama_id'] ?? null,
                    'insentif_utama' => (float) ($item['insentif_utama'] ?? 0),
                    'karyawan_pendukung_1_id' => $item['karyawan_pendukung_1_id'] ?? null,
                    'insentif_pendukung_1' => (float) ($item['insentif_pendukung_1'] ?? 0),
                    'karyawan_pendukung_2_id' => $item['karyawan_pendukung_2_id'] ?? null,
                    'insentif_pendukung_2' => (float) ($item['insentif_pendukung_2'] ?? 0),
                ];

                continue;
            }

            if ($tipe !== 'produk') {
                throw ValidationException::withMessages([
                    "items.{$index}.tipe" => 'Tipe item transaksi tidak valid.',
                ]);
            }

            $produkQuery = Produk::withoutGlobalScopes()
                ->with('jenisProduk')
                ->where('id', $item['item_id'])
                ->where('status', 'aktif')
                ->forTransaksi()
                ->lockForUpdate();

            $this->applyActorCabang($produkQuery, $actor);

            $produk = $produkQuery->first();

            if (!$produk) {
                throw ValidationException::withMessages([
                    "items.{$index}.item_id" => 'Produk tidak valid untuk transaksi atau tidak aktif.',
                ]);
            }

            $jumlah = (int) ($item['jumlah'] ?? 0);

            if ($jumlah > (int) $produk->stok) {
                throw ValidationException::withMessages([
                    "items.{$index}.jumlah" => "Stok produk {$produk->nama} tidak mencukupi. Stok tersedia: {$produk->stok}.",
                ]);
            }

            $hargaSatuan = $produk->harga_fleksibel
                ? (float) ($item['harga_satuan'] ?? 0)
                : (float) $produk->harga_jual;

            $normalizedItems[] = [
                'tipe' => 'produk',
                'layanan_id' => null,
                'produk_id' => $produk->id,
                'nama_item' => $produk->nama,
                'jumlah' => $jumlah,
                'satuan' => $produk->satuan ?: 'pcs',
                'harga_satuan' => $hargaSatuan,
                'total' => $hargaSatuan * $jumlah,
                'karyawan_utama_id' => null,
                'insentif_utama' => 0,
                'karyawan_pendukung_1_id' => null,
                'insentif_pendukung_1' => 0,
                'karyawan_pendukung_2_id' => null,
                'insentif_pendukung_2' => 0,
            ];
        }

        return $normalizedItems;
    }

    private function resolveAmounts(array $normalizedItems, array $payload): array
    {
        $subtotal = (float) collect($normalizedItems)->sum('total');
        $diskon = (float) ($payload['diskon'] ?? 0);

        if ($diskon > $subtotal) {
            throw ValidationException::withMessages([
                'diskon' => 'Diskon tidak boleh lebih besar dari subtotal transaksi.',
            ]);
        }

        $total = $subtotal - $diskon;

        if ($total < 0) {
            throw ValidationException::withMessages([
                'diskon' => 'Total transaksi tidak boleh bernilai negatif.',
            ]);
        }

        $bayar = (float) ($payload['bayar'] ?? 0);

        if ($bayar < $total) {
            throw ValidationException::withMessages([
                'bayar' => 'Nominal bayar wajib sama atau lebih besar dari total transaksi.',
            ]);
        }

        $kembalian = max(0, $bayar - $total);

        return [
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'total' => $total,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'status_pembayaran' => 'lunas',
            'status' => 'selesai',
        ];
    }

    private function persistDetails(Transaksi $transaksi, array $normalizedItems, ?User $actor = null): void
    {
        foreach ($normalizedItems as $item) {
            if ($item['tipe'] === 'produk') {
                $produkQuery = Produk::withoutGlobalScopes()
                    ->where('id', $item['produk_id'])
                    ->lockForUpdate();

                $this->applyActorCabang($produkQuery, $actor);

                $produkQuery->firstOrFail()->decrement('stok', $item['jumlah']);
            }

            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'tipe' => $item['tipe'],
                'layanan_id' => $item['layanan_id'],
                'produk_id' => $item['produk_id'],
                'nama_item' => $item['nama_item'],
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'total' => $item['total'],
                'karyawan_utama_id' => $item['karyawan_utama_id'],
                'insentif_utama' => $item['insentif_utama'],
                'karyawan_pendukung_1_id' => $item['karyawan_pendukung_1_id'],
                'insentif_pendukung_1' => $item['insentif_pendukung_1'],
                'karyawan_pendukung_2_id' => $item['karyawan_pendukung_2_id'],
                'insentif_pendukung_2' => $item['insentif_pendukung_2'],
            ]);
        }
    }

    private function restoreProductStock(Transaksi $transaksi, ?User $actor = null): void
    {
        $transaksi->loadMissing('details');

        foreach ($transaksi->details as $detail) {
            if (!$detail->produk_id) {
                continue;
            }

            $produkQuery = Produk::withoutGlobalScopes()
                ->where('id', $detail->produk_id)
                ->lockForUpdate();

            $this->applyActorCabang($produkQuery, $actor);

            $produkQuery->first()?->increment('stok', $detail->jumlah);
        }
    }

    private function syncPelangganVisitStats(int $pelangganId, ?User $actor = null): void
    {
        $pelanggan = Pelanggan::find($pelangganId);

        if (!$pelanggan) {
            return;
        }

        $summaryQuery = Transaksi::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('pelanggan_id', $pelangganId)
            ->selectRaw('COUNT(*) as total_kunjungan, MAX(tanggal_transaksi) as terakhir_kunjungan');

        $this->applyActorCabang($summaryQuery, $actor);

        $summary = $summaryQuery->first();

        $pelanggan->update([
            'total_kunjungan' => (int) ($summary->total_kunjungan ?? 0),
            'terakhir_kunjungan' => $summary->terakhir_kunjungan,
        ]);
    }

    private function validateKaryawanAssignments(array $item, int $index, ?User $actor = null): void
    {
        $karyawanIds = array_values(array_filter([
            $item['karyawan_utama_id'] ?? null,
            $item['karyawan_pendukung_1_id'] ?? null,
            $item['karyawan_pendukung_2_id'] ?? null,
        ]));

        if (empty($item['karyawan_utama_id'])) {
            throw ValidationException::withMessages([
                "items.{$index}.karyawan_utama_id" => 'Karyawan utama wajib diisi untuk item layanan.',
            ]);
        }

        if (count($karyawanIds) !== count(array_unique($karyawanIds))) {
            throw ValidationException::withMessages([
                "items.{$index}.karyawan_utama_id" => 'Karyawan utama dan pendukung tidak boleh sama pada item layanan yang sama.',
            ]);
        }

        $workerQuery = Karyawan::query()
            ->whereIn('id', $karyawanIds)
            ->where('status_kerja', 'aktif');

        if ($actor && !$actor->isSuperAdmin()) {
            $workerQuery->where('cabang_id', $actor->cabang_id);
        }

        $validWorkers = $workerQuery->count();

        if ($validWorkers !== count($karyawanIds)) {
            throw ValidationException::withMessages([
                "items.{$index}.karyawan_utama_id" => 'Data karyawan pada item layanan tidak valid atau tidak aktif.',
            ]);
        }
    }

    private function generateNomorTransaksi(string $tanggalTransaksi): string
    {
        return 'TRX-' . Carbon::parse($tanggalTransaksi)->format('Ymd') . '-' . strtoupper(uniqid());
    }

    private function buildDraftSyncTargets($tanggalTransaksi, iterable $items): array
    {
        $periode = Carbon::parse($tanggalTransaksi)->format('Y-m');
        $targets = [];

        foreach ($items as $item) {
            $karyawanIds = array_values(array_filter([
                $item['karyawan_utama_id'] ?? null,
                $item['karyawan_pendukung_1_id'] ?? null,
                $item['karyawan_pendukung_2_id'] ?? null,
            ]));

            foreach ($karyawanIds as $karyawanId) {
                $targets[] = [
                    'karyawan_id' => (int) $karyawanId,
                    'periode' => $periode,
                ];
            }
        }

        return $targets;
    }

    private function syncDraftGajis(array $targets): void
    {
        $this->gajiDraftSyncService->sync($targets);
    }

    private function applyActorCabang($query, ?User $actor): void
    {
        if (!$actor || $actor->isSuperAdmin()) {
            return;
        }

        if ($actor->cabang_id) {
            $query->where('cabang_id', $actor->cabang_id);
            return;
        }

        $query->whereNull('cabang_id');
    }
}
