<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_create_an_account_the_user_can_then_sign_in_with(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Nadia Syafiqah',
            'email' => 'nadia@nova.edu',
            'role' => 'student',
            'department' => 'PE',
            'password' => 'Str0ng!Passw0rd',
            'password_confirmation' => 'Str0ng!Passw0rd',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'nadia@nova.edu',
            'role' => 'student',
            'department' => 'PE',
        ]);

        // Verified on creation, since NOVA sends no email.
        $this->assertNotNull(User::where('email', 'nadia@nova.edu')->first()->email_verified_at);

        auth()->logout();

        $this->post('/login', [
            'email' => 'nadia@nova.edu',
            'password' => 'Str0ng!Passw0rd',
        ])->assertRedirect('/dashboard');
    }

    public function test_a_department_is_required_for_students_and_lecturers(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'No Department',
            'email' => 'nodept@nova.edu',
            'role' => 'student',
            'password' => 'Str0ng!Passw0rd',
            'password_confirmation' => 'Str0ng!Passw0rd',
        ])->assertSessionHasErrors('department');

        $this->assertDatabaseMissing('users', ['email' => 'nodept@nova.edu']);
    }

    public function test_department_must_be_a_known_value(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Free Text',
            'email' => 'freetext@nova.edu',
            'role' => 'student',
            'department' => 'Engineering',
            'password' => 'Str0ng!Passw0rd',
            'password_confirmation' => 'Str0ng!Passw0rd',
        ])->assertSessionHasErrors('department');
    }

    public function test_an_admin_account_is_created_without_a_department(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Second Admin',
            'email' => 'admin2@nova.edu',
            'role' => 'admin',
            'department' => 'SPT', // should be discarded
            'password' => 'Str0ng!Passw0rd',
            'password_confirmation' => 'Str0ng!Passw0rd',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'admin2@nova.edu', 'department' => null]);
    }

    public function test_the_password_policy_applies_to_admin_created_accounts(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Weak Password',
            'email' => 'weak@nova.edu',
            'role' => 'student',
            'department' => 'SPT',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    public function test_the_password_cannot_contain_the_account_holders_own_name(): void
    {
        // The rule used to read Auth::user()->name — the admin's name — which
        // was the wrong person entirely once an admin sets someone else's.
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Hanafi',
            'email' => 'hanafi@nova.edu',
            'role' => 'lecturer',
            'department' => 'PE',
            'password' => 'Hanafi!Passw0rd',
            'password_confirmation' => 'Hanafi!Passw0rd',
        ])->assertSessionHasErrors('password');
    }

    public function test_an_admin_cannot_change_their_own_role(): void
    {
        // No artisan recovery path exists, so the last admin demoting
        // themselves would lock everyone out of the admin area for good.
        $this->actingAs($this->admin)->patch("/admin/users/{$this->admin->id}", [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role' => 'student',
            'department' => 'SPT',
        ])->assertSessionHasErrors('role');

        $this->assertSame('admin', $this->admin->fresh()->role);
    }

    public function test_an_admin_cannot_delete_their_own_account(): void
    {
        $this->actingAs($this->admin)
            ->delete("/admin/users/{$this->admin->id}")
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_a_lecturer_who_owns_modules_cannot_be_deleted(): void
    {
        $lecturer = User::factory()->lecturer('SPT')->create();
        Course::create([
            'code' => 'SPT101', 'name' => 'Module', 'department' => 'SPT',
            'lecturer_id' => $lecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);

        // courses.lecturer_id is ON DELETE CASCADE, so allowing this would
        // silently destroy the modules and everything under them.
        $this->actingAs($this->admin)
            ->delete("/admin/users/{$lecturer->id}")
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $lecturer->id]);
        $this->assertDatabaseCount('courses', 1);
    }

    /* ------------------------------------------------------------------ */

    public function test_a_module_cannot_be_assigned_to_a_lecturer_from_another_department(): void
    {
        $sptLecturer = User::factory()->lecturer('SPT')->create();

        $this->actingAs($this->admin)->post('/admin/courses', [
            'code' => 'PE200',
            'name' => 'Cross-department module',
            'department' => 'PE',
            'lecturer_id' => $sptLecturer->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ])->assertSessionHasErrors('lecturer_id');

        $this->assertDatabaseMissing('courses', ['code' => 'PE200']);
    }

    public function test_admin_can_create_a_module_for_either_department(): void
    {
        $peLecturer = User::factory()->lecturer('PE')->create();

        $this->actingAs($this->admin)->post('/admin/courses', [
            'code' => 'PE200',
            'name' => 'Strategic Assessment',
            'department' => 'PE',
            'lecturer_id' => $peLecturer->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ])->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas('courses', ['code' => 'PE200', 'department' => 'PE']);
    }

    public function test_a_student_cannot_be_enrolled_in_another_departments_module(): void
    {
        $peLecturer = User::factory()->lecturer('PE')->create();
        $sptStudent = User::factory()->student('SPT')->create();

        $peCourse = Course::create([
            'code' => 'PE101', 'name' => 'PE Module', 'department' => 'PE',
            'lecturer_id' => $peLecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);

        $this->actingAs($this->admin)
            ->post("/admin/courses/{$peCourse->id}/students", ['user_id' => $sptStudent->id])
            ->assertSessionHasErrors('user_id');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_admin_can_enrol_and_remove_a_student_in_the_same_department(): void
    {
        $peLecturer = User::factory()->lecturer('PE')->create();
        $peStudent = User::factory()->student('PE')->create();

        $peCourse = Course::create([
            'code' => 'PE101', 'name' => 'PE Module', 'department' => 'PE',
            'lecturer_id' => $peLecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);

        $this->actingAs($this->admin)
            ->post("/admin/courses/{$peCourse->id}/students", ['user_id' => $peStudent->id])
            ->assertRedirect();

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $peStudent->id,
            'course_id' => $peCourse->id,
        ]);

        $this->actingAs($this->admin)
            ->delete("/admin/courses/{$peCourse->id}/students/{$peStudent->id}")
            ->assertRedirect();

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_a_module_with_enrolled_students_cannot_change_department(): void
    {
        $peLecturer = User::factory()->lecturer('PE')->create();
        $sptLecturer = User::factory()->lecturer('SPT')->create();
        $peStudent = User::factory()->student('PE')->create();

        $peCourse = Course::create([
            'code' => 'PE101', 'name' => 'PE Module', 'department' => 'PE',
            'lecturer_id' => $peLecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);
        Enrollment::create(['user_id' => $peStudent->id, 'course_id' => $peCourse->id]);

        $this->actingAs($this->admin)->patch("/admin/courses/{$peCourse->id}", [
            'code' => 'PE101',
            'name' => 'PE Module',
            'department' => 'SPT',
            'lecturer_id' => $sptLecturer->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ])->assertSessionHasErrors('department');

        $this->assertSame('PE', $peCourse->fresh()->department);
    }
}
