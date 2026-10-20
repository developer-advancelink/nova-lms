<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The root path is a dispatcher, never a page: it has only ever redirected.
 * Guests go to the login screen, and everyone else to their role's dashboard
 * via User::homePath(), which is also what the role middleware bounces to.
 */
class RootRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_screen(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_students_are_redirected_to_the_student_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $this->actingAs($user)->get('/')->assertRedirect('/dashboard');
    }

    public function test_lecturers_are_redirected_to_the_lecturer_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'lecturer']);

        $this->actingAs($user)->get('/')->assertRedirect('/lecturer/dashboard');
    }

    public function test_admins_are_redirected_to_the_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get('/')->assertRedirect('/admin/dashboard');
    }
}
