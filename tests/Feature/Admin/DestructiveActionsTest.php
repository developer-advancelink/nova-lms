<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * The delete and remove buttons on the admin screens.
 *
 * These existed as server-side guards already, but every button was wired with
 * onsubmit="return confirm(...)". A native confirm() returns false wherever
 * dialogs are blocked — an embedded browser, or Chrome after the user ticks
 * "don't allow this page to create more dialogs" — and the form then silently
 * never submits, so every destructive button looked dead while the server was
 * perfectly fine. The first test here is the tripwire against reintroducing
 * that pattern; the rest pin the happy paths that were impossible to reach.
 */
class DestructiveActionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_no_view_relies_on_a_native_confirm_dialog(): void
    {
        $offenders = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            // Skip the shared modal partial, whose docblock explains the
            // pattern it replaces.
            if ($file->getFilename() === '_confirm-delete.blade.php') {
                continue;
            }

            if (str_contains($file->getContents(), 'onsubmit=')) {
                $offenders[] = $file->getRelativePathname();
            }
        }

        $this->assertSame([], $offenders,
            'These views gate a form on an inline onsubmit handler. Use the in-page '
            .'confirmation modal (pages.admin._confirm-delete) instead — a blocked '
            .'confirm() returns false and the form silently never submits.');
    }

    public function test_admin_can_delete_an_account_with_nothing_attached(): void
    {
        $student = User::factory()->student('SPT')->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $student))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }

    public function test_admin_can_delete_a_module_with_no_enrolled_students(): void
    {
        $lecturer = User::factory()->lecturer('PE')->create();

        $course = Course::create([
            'code' => 'PE102', 'name' => 'Strategic Assessment', 'department' => 'PE',
            'lecturer_id' => $lecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_a_module_with_enrolled_students_cannot_be_deleted(): void
    {
        $lecturer = User::factory()->lecturer('PE')->create();
        $student = User::factory()->student('PE')->create();

        $course = Course::create([
            'code' => 'PE101', 'name' => 'PE Module', 'department' => 'PE',
            'lecturer_id' => $lecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);
        Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $course))
            ->assertSessionHasErrors('course');

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_admin_can_remove_a_student_from_a_roster(): void
    {
        $lecturer = User::factory()->lecturer('PE')->create();
        $student = User::factory()->student('PE')->create();

        $course = Course::create([
            'code' => 'PE101', 'name' => 'PE Module', 'department' => 'PE',
            'lecturer_id' => $lecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);
        Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.students.destroy', [$course, $student]))
            ->assertRedirect();

        $this->assertDatabaseCount('enrollments', 0);
        // The account itself survives — only the enrollment goes.
        $this->assertDatabaseHas('users', ['id' => $student->id]);
    }

    public function test_removing_a_student_who_is_not_enrolled_404s(): void
    {
        $lecturer = User::factory()->lecturer('PE')->create();
        $outsider = User::factory()->student('PE')->create();

        $course = Course::create([
            'code' => 'PE101', 'name' => 'PE Module', 'department' => 'PE',
            'lecturer_id' => $lecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);

        // scopeBindings() resolves {student} through Course::students().
        $this->actingAs($this->admin)
            ->delete(route('admin.courses.students.destroy', [$course, $outsider]))
            ->assertNotFound();
    }
}
