<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->student('SPT')->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        // Login lands on the role's own dashboard, not a shared one.
        $response->assertRedirect('/dashboard');
    }

    public function test_each_role_is_redirected_to_its_own_dashboard_on_login(): void
    {
        $cases = [
            [User::factory()->admin()->create(), '/admin/dashboard'],
            [User::factory()->lecturer('SPT')->create(), '/lecturer/dashboard'],
            [User::factory()->student('PE')->create(), '/dashboard'],
        ];

        foreach ($cases as [$user, $expected]) {
            $this->post('/login', ['email' => $user->email, 'password' => 'password'])
                ->assertRedirect($expected);

            $this->post('/logout');
        }
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
