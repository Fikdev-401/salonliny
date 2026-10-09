<?php

namespace App\Models;

use App\Models\Concerns\HasCabangScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventaris extends Model
{
    use HasFactory, HasCabangScope;

    protected $table = 'inventaris';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'jenis_aset',
        'jumlah',
        'satuan',
        'tanggal_input',
        'status',
        'keterangan',
        'cabang_id',
        'created_by',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tanggal_input' => 'date',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }
}
