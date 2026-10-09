<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluarans';

    protected $fillable = [
        'nomor_pengeluaran',
        'tanggal',
        'kategori',
        'nama_pengeluaran',
        'jumlah',
        'keterangan',
        'bukti_nota',
        'user_id',
        'sumber_tipe',
        'sumber_id',
        'is_system_generated',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'is_system_generated' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isLinkedTo(string $type, int $id): bool
    {
        return $this->sumber_tipe === $type && (int) $this->sumber_id === $id;
    }
}
