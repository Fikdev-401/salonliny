<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_non_super_admin_cannot_access_user_management_routes(): void
    {
        $kasir = $this->createUserWithRole('kasir');

        $response = $this
            ->actingAs($kasir)
            ->from('/dashboard')
            ->get('/users');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    public function test_super_admin_can_access_user_management_routes(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');

        $response = $this
            ->actingAs($superAdmin)
            ->get('/users');

        $response->assertOk();
    }
}
