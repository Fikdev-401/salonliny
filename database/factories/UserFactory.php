<?php

namespace Database\Factories;

use App\Models\Cabang;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'nama' => 'Admin',
                'deskripsi' => 'Role admin default untuk testing',
            ]
        );

        $cabang = Cabang::firstOrCreate(
            ['is_default' => true],
            [
                'nama' => 'Cabang Utama',
                'alamat' => 'Alamat Cabang Utama',
                'status' => 'aktif',
                'is_default' => true,
            ]
        );

        return [
            'nama' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => $role->id,
            'cabang_id' => $cabang->id,
            'status' => 'aktif',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
