<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Self-registration is deliberately disabled on NOVA — accounts are created by
 * an administrator, because only an admin may set `role` and `department`.
 * These tests pin that decision so the Breeze routes are not quietly restored.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_new_users_cannot_self_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Str0ng!Passw0rd',
            'password_confirmation' => 'Str0ng!Passw0rd',
        ]);

        $response->assertNotFound();
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_an_account_created_by_an_admin_can_sign_in(): void
    {
        // The replacement path: an admin creates the account with a known
        // initial password, which the user can then use.
        $user = User::factory()->create([
            'email' => 'created@nova.edu',
            'password' => 'Str0ng!Passw0rd',
            'role' => 'student',
            'department' => 'SPT',
        ]);

        $this->post('/login', [
            'email' => 'created@nova.edu',
            'password' => 'Str0ng!Passw0rd',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }
}
