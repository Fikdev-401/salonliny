<?php

namespace App\Services;

use App\Models\Inventaris;
use App\Models\JenisProduk;
use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembelianWorkflowService
{
    public function __construct(
        private readonly PengeluaranSyncService $pengeluaranSyncService
    ) {
    }

    public function create(array $payload, User $actor): Pembelian
    {
        return DB::transaction(function () use ($payload, $actor) {
            [$produk, $inventaris] = $this->resolveSourceModels($payload, null, $actor);

            $pembelian = Pembelian::create([
                'nomor_nota' => $this->generateNomorNota($payload['tanggal']),
                'tanggal' => $payload['tanggal'],
                'jenis_pembelian' => $payload['jenis_pembelian'],
                'produk_id' => $produk?->id,
                'inventaris_id' => $inventaris?->id,
                'jumlah' => (int) $payload['jumlah'],
                'harga_satuan' => (float) $payload['harga_satuan'],
                'total' => (float) $payload['jumlah'] * (float) $payload['harga_satuan'],
                'supplier' => $payload['supplier'] ?? null,
                'no_hp_supplier' => $payload['no_hp_supplier'] ?? null,
                'keterangan' => $payload['keterangan'] ?? null,
                'user_id' => $actor->id,
            ]);

            if ($produk) {
                $produk->increment('stok', (int) $payload['jumlah']);
                $produk->update(['harga_beli' => $payload['harga_satuan']]);
            }

            $this->pengeluaranSyncService->syncForPembelian($pembelian->fresh(['produk', 'inventaris']));

            return $pembelian->fresh(['produk.jenisProduk', 'inventaris', 'user', 'pengeluaran']);
        });
    }

    public function update(Pembelian $pembelian, array $payload, User $actor): Pembelian
    {
        return DB::transaction(function () use ($pembelian, $payload, $actor) {
            $oldJenis = $pembelian->jenis_pembelian;
            $oldJumlah = (int) $pembelian->jumlah;
            $oldProduk = $this->findProdukForActor((int) $pembelian->produk_id, $actor);
            $oldInventaris = $pembelian->inventaris_id ? $pembelian->inventaris : null;
            $sameProductFlow = $oldProduk
                && $payload['jenis_pembelian'] !== 'alat_inventaris'
                && (int) $payload['produk_id'] === (int) $pembelian->produk_id;
            [$produk, $inventaris] = $this->resolveSourceModels($payload, $pembelian, $actor);

            $this->reverseOldEffectIfNeeded($pembelian, $payload, $oldProduk);

            if ($oldJenis === 'alat_inventaris' && $payload['jenis_pembelian'] !== 'alat_inventaris' && $oldInventaris) {
                $oldInventaris->delete();
            }

            $pembelian->update([
                'tanggal' => $payload['tanggal'],
                'jenis_pembelian' => $payload['jenis_pembelian'],
                'produk_id' => $produk?->id,
                'inventaris_id' => $inventaris?->id,
                'jumlah' => (int) $payload['jumlah'],
                'harga_satuan' => (float) $payload['harga_satuan'],
                'total' => (float) $payload['jumlah'] * (float) $payload['harga_satuan'],
                'supplier' => $payload['supplier'] ?? null,
                'no_hp_supplier' => $payload['no_hp_supplier'] ?? null,
                'keterangan' => $payload['keterangan'] ?? null,
                'user_id' => $actor->id,
            ]);

            if ($produk) {
                if ($sameProductFlow) {
                    $delta = (int) $payload['jumlah'] - $oldJumlah;

                    if ($delta > 0) {
                        $produk->increment('stok', $delta);
                    }
                } else {
                    $produk->increment('stok', (int) $payload['jumlah']);
                }

                $produk->update(['harga_beli' => $payload['harga_satuan']]);
            }

            $this->pengeluaranSyncService->syncForPembelian($pembelian->fresh(['produk', 'inventaris']));

            return $pembelian->fresh(['produk.jenisProduk', 'inventaris', 'user', 'pengeluaran']);
        });
    }

    public function delete(Pembelian $pembelian, User $actor): void
    {
        DB::transaction(function () use ($pembelian, $actor) {
            $this->reverseOldEffectIfNeeded($pembelian, null, $this->findProdukForActor((int) $pembelian->produk_id, $actor));

            if ($pembelian->jenis_pembelian === 'alat_inventaris' && $pembelian->inventaris) {
                $pembelian->inventaris->delete();
            }

            $this->pengeluaranSyncService->deleteForPembelian($pembelian);
            $pembelian->delete();
        });
    }

    private function resolveSourceModels(array $payload, ?Pembelian $existingPembelian, User $actor): array
    {
        if ($payload['jenis_pembelian'] === 'alat_inventaris') {
            $inventaris = $existingPembelian?->inventaris;

            if (!$inventaris) {
                $inventaris = new Inventaris();
                $inventaris->created_by = $actor->id;
                $inventaris->cabang_id = $actor->cabang_id;
            }

            $inventaris->fill([
                'kode_aset' => ($payload['kode_aset'] ?? null) ?: $this->generateKodeAset($payload['tanggal']),
                'nama_aset' => $payload['nama_aset'],
                'jenis_aset' => $payload['jenis_aset'],
                'jumlah' => (int) $payload['jumlah'],
                'satuan' => $payload['satuan_aset'],
                'tanggal_input' => $payload['tanggal'],
                'status' => $payload['status_aset'],
                'keterangan' => $payload['keterangan_aset'] ?? $payload['keterangan'] ?? null,
            ]);
            $inventaris->save();

            return [null, $inventaris];
        }

        $produkQuery = Produk::withoutGlobalScopes()
            ->with('jenisProduk')
            ->where('id', $payload['produk_id'])
            ->where('status', 'aktif')
            ->lockForUpdate();

        $this->applyActorCabang($produkQuery, $actor);

        $produk = $produkQuery->first();

        if (!$produk) {
            throw ValidationException::withMessages([
                'produk_id' => 'Produk pembelian tidak valid atau tidak aktif.',
            ]);
        }

        $expectedSlug = $this->resolveExpectedProdukSlug($payload['jenis_pembelian']);

        if ($produk->jenisProduk?->slug !== $expectedSlug) {
            $expectedName = JenisProduk::query()->where('slug', $expectedSlug)->value('nama') ?? $expectedSlug;

            throw ValidationException::withMessages([
                'produk_id' => "Produk harus bertipe {$expectedName} untuk jenis pembelian ini.",
            ]);
        }

        return [$produk, null];
    }

    private function resolveExpectedProdukSlug(string $jenisPembelian): string
    {
        return match ($jenisPembelian) {
            'bahan_pakai' => 'bahan-pakai',
            'barang_sewa' => 'barang-sewa',
            default => 'produk-jual',
        };
    }

    private function generateNomorNota(string $tanggal): string
    {
        return 'PMB-' . Carbon::parse($tanggal)->format('Ymd') . '-' . strtoupper(uniqid());
    }

    private function generateKodeAset(string $tanggal): string
    {
        return 'AST-' . Carbon::parse($tanggal)->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    private function reverseOldEffectIfNeeded(Pembelian $pembelian, ?array $newPayload, ?Produk $oldProduk): void
    {
        if (!$oldProduk) {
            return;
        }

        $sameProductFlow = $newPayload
            && $newPayload['jenis_pembelian'] !== 'alat_inventaris'
            && (int) $newPayload['produk_id'] === (int) $pembelian->produk_id;

        if ($sameProductFlow) {
            $delta = (int) $newPayload['jumlah'] - (int) $pembelian->jumlah;

            if ($delta < 0) {
                $jumlahPengurang = abs($delta);

                if ($oldProduk->stok < $jumlahPengurang) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Jumlah pembelian baru tidak bisa lebih kecil karena stok hasil pembelian lama sudah terpakai.',
                    ]);
                }

                $oldProduk->decrement('stok', $jumlahPengurang);
            }

            return;
        }

        if ($oldProduk->stok < $pembelian->jumlah) {
            throw ValidationException::withMessages([
                'pembelian' => 'Pembelian tidak bisa diubah/dihapus karena stok hasil pembelian ini sudah digunakan.',
            ]);
        }

        $oldProduk->decrement('stok', $pembelian->jumlah);
    }

    private function findProdukForActor(int $produkId, User $actor): ?Produk
    {
        if (!$produkId) {
            return null;
        }

        $query = Produk::withoutGlobalScopes()
            ->whereKey($produkId)
            ->lockForUpdate();

        $this->applyActorCabang($query, $actor);

        return $query->first();
    }

    private function applyActorCabang($query, User $actor): void
    {
        if ($actor->isSuperAdmin()) {
            return;
        }

        if ($actor->cabang_id) {
            $query->where('cabang_id', $actor->cabang_id);
            return;
        }

        $query->whereNull('cabang_id');
    }
}
