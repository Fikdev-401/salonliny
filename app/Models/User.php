<?php

namespace App\Models;

use App\Models\Concerns\HasCabangScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasCabangScope;

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role_id',
        'cabang_id',
        'no_hp',
        'alamat',
        'status',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class);
    }

    // Helper methods untuk cek role
    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super-admin';
    }

    public function isPimpinan(): bool
    {
        return $this->role?->slug === 'pimpinan';
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role?->slug === 'kasir';
    }

    public function isKaryawanRole(): bool
    {
        return $this->role?->slug === 'karyawan';
    }

    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role?->slug, $roles);
        }
        return $this->role?->slug === $roles;
    }
}
