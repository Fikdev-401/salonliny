<?php

namespace Tests\Concerns;

use App\Models\Cabang;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesDomainData
{
    protected function createCabang(array $attributes = []): Cabang
    {
        return Cabang::create(array_merge([
            'nama' => 'Cabang Utama',
            'alamat' => 'Alamat Cabang Utama',
            'no_telepon' => null,
            'email' => null,
            'status' => 'aktif',
            'is_default' => true,
        ], $attributes));
    }

    protected function createRole(string $slug, ?string $nama = null): Role
    {
        return Role::firstOrCreate(
            ['slug' => $slug],
            [
                'nama' => $nama ?? Str::headline(str_replace('-', ' ', $slug)),
                'deskripsi' => 'Role untuk testing',
            ]
        );
    }

    protected function createUserWithRole(string $roleSlug, array $attributes = []): User
    {
        $role = $this->createRole($roleSlug);
        $cabang = Cabang::query()->first() ?? $this->createCabang();

        return User::create(array_merge([
            'nama' => ucfirst($roleSlug) . ' Test',
            'username' => str_replace('-', '', $roleSlug) . '_' . Str::lower(Str::random(6)),
            'email' => str_replace('-', '', $roleSlug) . '_' . Str::lower(Str::random(6)) . '@example.test',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'cabang_id' => $cabang->id,
            'status' => 'aktif',
        ], $attributes));
    }
}
