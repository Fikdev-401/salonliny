<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainData;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    use CreatesDomainData;

    public function test_login_page_is_accessible(): void
    {
        $this->get('/login')
            ->assertOk();
    }

    public function test_user_can_login_and_is_redirected_based_on_role(): void
    {
        $kasir = $this->createUserWithRole('kasir', [
            'username' => 'kasir_test',
            'email' => 'kasir_test@example.test',
        ]);

        $response = $this->post('/login', [
            'username' => 'kasir_test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/transaksi');
        $this->assertAuthenticatedAs($kasir);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->createUserWithRole('admin', [
            'username' => 'admin_test',
            'email' => 'admin_test@example.test',
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin_test',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->createUserWithRole('admin', [
            'username' => 'inactive_admin',
            'email' => 'inactive_admin@example.test',
            'status' => 'nonaktif',
        ]);

        $this->from('/login')->post('/login', [
            'username' => 'inactive_admin',
            'password' => 'password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_login_is_throttled_after_repeated_failures(): void
    {
        $this->createUserWithRole('admin', [
            'username' => 'throttle_admin',
            'email' => 'throttle_admin@example.test',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'username' => 'throttle_admin',
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post('/login', [
            'username' => 'throttle_admin',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_user_must_change_temporary_password_before_accessing_app(): void
    {
        $user = $this->createUserWithRole('admin', [
            'username' => 'temporary_admin',
            'email' => 'temporary_admin@example.test',
            'must_change_password' => true,
        ]);

        $this->post('/login', [
            'username' => 'temporary_admin',
            'password' => 'password',
        ])->assertRedirect(route('password.change'));

        $this->actingAs($user)->get('/dashboard')
            ->assertRedirect(route('password.change'));

        $this->actingAs($user)->post('/change-password', [
            'current_password' => 'password',
            'new_password' => 'Password123',
            'new_password_confirmation' => 'Password123',
        ])->assertSessionHas('success');

        $this->assertFalse($user->fresh()->must_change_password);
    }

    public function test_admin_reset_password_generates_temporary_password_and_forces_change(): void
    {
        $superAdmin = $this->createUserWithRole('super-admin');
        $user = $this->createUserWithRole('admin', [
            'username' => 'reset_admin',
            'email' => 'reset_admin@example.test',
        ]);

        $this->actingAs($superAdmin)
            ->post(route('users.reset-password', $user))
            ->assertRedirect();

        $user->refresh();

        $this->assertTrue($user->must_change_password);
        $this->assertFalse(\Illuminate\Support\Facades\Hash::check('password', $user->password));
    }
}
