<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pins the two guarantees this feature exists to provide:
 *
 *   1. Department isolation — SPT cannot see or touch PE, and vice versa.
 *   2. Ownership — within a department, students must be enrolled and
 *      lecturers must own what they modify.
 *
 * Both matter. `$sptOutsider` is the row that tells them apart: a student in
 * the right department with no enrollment. Without it, a suite can pass on
 * department isolation alone and look like it covers both.
 */
class DepartmentIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $sptLecturer;
    private User $sptStudent;
    private User $sptOutsider;
    private User $peLecturer;
    private User $peStudent;
    private User $admin;

    private Course $sptCourse;
    private Course $peCourse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin       = User::factory()->admin()->create();
        $this->sptLecturer = User::factory()->lecturer('SPT')->create();
        $this->sptStudent  = User::factory()->student('SPT')->create();
        $this->sptOutsider = User::factory()->student('SPT')->create();
        $this->peLecturer  = User::factory()->lecturer('PE')->create();
        $this->peStudent   = User::factory()->student('PE')->create();

        $this->sptCourse = Course::create([
            'code' => 'SPT101', 'name' => 'SPT Module', 'department' => 'SPT',
            'lecturer_id' => $this->sptLecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);

        $this->peCourse = Course::create([
            'code' => 'PE101', 'name' => 'PE Module', 'department' => 'PE',
            'lecturer_id' => $this->peLecturer->id,
            'start_date' => '2026-01-01', 'end_date' => '2026-12-31',
        ]);

        Enrollment::create(['user_id' => $this->sptStudent->id, 'course_id' => $this->sptCourse->id]);
        Enrollment::create(['user_id' => $this->peStudent->id, 'course_id' => $this->peCourse->id]);
    }

    /* ---------------------------------------------------------------------
     | The predicate
     * ------------------------------------------------------------------ */

    public function test_visible_to_scopes_by_department_and_ownership(): void
    {
        $this->assertSame(['SPT101'], Course::visibleTo($this->sptLecturer)->pluck('code')->all());
        $this->assertSame(['SPT101'], Course::visibleTo($this->sptStudent)->pluck('code')->all());
        $this->assertSame(['PE101'], Course::visibleTo($this->peLecturer)->pluck('code')->all());
        $this->assertSame(['PE101'], Course::visibleTo($this->peStudent)->pluck('code')->all());

        // Right department, no enrollment.
        $this->assertSame([], Course::visibleTo($this->sptOutsider)->pluck('code')->all());

        // Admin crosses the boundary; a guest sees nothing.
        $this->assertCount(2, Course::visibleTo($this->admin)->get());
        $this->assertSame([], Course::visibleTo(null)->pluck('code')->all());
    }

    public function test_a_user_without_a_department_sees_nothing(): void
    {
        $orphan = User::factory()->create(['role' => 'student', 'department' => null]);

        // Guards against where('department', null) compiling to "is null",
        // which would match every NULL-department row instead of excluding.
        $this->assertSame([], Course::visibleTo($orphan)->pluck('code')->all());
    }

    /* ---------------------------------------------------------------------
     | Students
     * ------------------------------------------------------------------ */

    public function test_student_cannot_view_a_module_in_another_department(): void
    {
        $this->actingAs($this->sptStudent)
            ->get("/courses/{$this->peCourse->id}")
            ->assertNotFound();
    }

    public function test_student_cannot_view_a_module_in_their_department_they_are_not_enrolled_in(): void
    {
        $this->actingAs($this->sptOutsider)
            ->get("/courses/{$this->sptCourse->id}")
            ->assertNotFound();
    }

    public function test_student_can_still_view_their_own_module(): void
    {
        $this->actingAs($this->sptStudent)
            ->get("/courses/{$this->sptCourse->id}")
            ->assertOk();
    }

    /* ---------------------------------------------------------------------
     | Lecturers
     * ------------------------------------------------------------------ */

    public function test_lecturer_cannot_view_another_departments_module(): void
    {
        $this->actingAs($this->sptLecturer)
            ->get("/lecturer/courses/{$this->peCourse->id}")
            ->assertNotFound();
    }

    public function test_lecturer_cannot_rename_another_departments_module(): void
    {
        $this->actingAs($this->sptLecturer)
            ->patch("/lecturer/courses/{$this->peCourse->id}", [
                'name' => 'pwned',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
            ])
            ->assertNotFound();

        $this->assertSame('PE Module', $this->peCourse->fresh()->name);
    }

    public function test_lecturer_cannot_add_content_to_another_departments_module(): void
    {
        $this->actingAs($this->sptLecturer)
            ->post("/lecturer/courses/{$this->peCourse->id}/announcements", [
                'title' => 'x', 'body' => 'y',
            ])
            ->assertNotFound();

        $this->actingAs($this->sptLecturer)
            ->post("/lecturer/courses/{$this->peCourse->id}/assignments", [
                'title' => 'x', 'due_date' => '2026-06-01',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('announcements', 0);
        $this->assertDatabaseCount('assignments', 0);
    }

    public function test_lecturer_cannot_delete_a_material_belonging_to_another_module(): void
    {
        $peMaterial = CourseMaterial::create([
            'course_id' => $this->peCourse->id,
            'title' => 'PE handbook',
        ]);

        // The old route ignored the course segment entirely, so passing your
        // own course id with someone else's material id deleted it. Scoped
        // bindings resolve {material} through $course->materials().
        $this->actingAs($this->sptLecturer)
            ->delete("/lecturer/courses/{$this->sptCourse->id}/materials/{$peMaterial->id}")
            ->assertNotFound();

        $this->assertDatabaseHas('course_materials', ['id' => $peMaterial->id]);
    }

    public function test_lecturer_cannot_grade_a_student_in_another_department(): void
    {
        $peAssignment = Assignment::create([
            'course_id' => $this->peCourse->id,
            'title' => 'PE paper',
            'due_date' => '2026-06-01',
        ]);

        // Assignment does not belong to the named course -> 404 on binding.
        $this->actingAs($this->sptLecturer)
            ->post("/lecturer/courses/{$this->sptCourse->id}/assignments/{$peAssignment->id}/grade/{$this->peStudent->id}", [
                'grade' => 'A',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_lecturer_cannot_grade_a_student_who_is_not_enrolled(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->sptCourse->id,
            'title' => 'SPT paper',
            'due_date' => '2026-06-01',
        ]);

        // Course and assignment both check out; the student does not.
        $this->actingAs($this->sptLecturer)
            ->post("/lecturer/courses/{$this->sptCourse->id}/assignments/{$assignment->id}/grade/{$this->sptOutsider->id}", [
                'grade' => 'A',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_lecturer_can_still_grade_their_own_enrolled_student(): void
    {
        $assignment = Assignment::create([
            'course_id' => $this->sptCourse->id,
            'title' => 'SPT paper',
            'due_date' => '2026-06-01',
        ]);

        $this->actingAs($this->sptLecturer)
            ->post("/lecturer/courses/{$this->sptCourse->id}/assignments/{$assignment->id}/grade/{$this->sptStudent->id}", [
                'grade' => 'A-',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('submissions', [
            'assignment_id' => $assignment->id,
            'user_id' => $this->sptStudent->id,
            'grade' => 'A-',
        ]);
    }

    /* ---------------------------------------------------------------------
     | Inbox
     * ------------------------------------------------------------------ */

    public function test_student_cannot_read_a_thread_from_another_department(): void
    {
        $peThread = Message::create([
            'sender_id' => $this->peLecturer->id,
            'receiver_id' => $this->peStudent->id,
            'course_id' => $this->peCourse->id,
            'subject' => 'PE only',
            'body' => 'Confidential to PE.',
        ]);

        $this->actingAs($this->sptStudent)
            ->get("/inbox?thread={$peThread->id}")
            ->assertOk()
            ->assertDontSee('Confidential to PE.');
    }

    public function test_student_cannot_tag_a_reply_with_another_departments_module(): void
    {
        $this->actingAs($this->sptStudent)
            ->post('/inbox/reply', [
                'receiver_id' => $this->sptLecturer->id,
                'course_id' => $this->peCourse->id,
                'body' => 'hello',
            ])
            ->assertSessionHasErrors('course_id');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_student_cannot_message_a_lecturer_in_another_department(): void
    {
        $this->actingAs($this->sptStudent)
            ->post('/inbox/reply', [
                'receiver_id' => $this->peLecturer->id,
                'body' => 'hello',
            ])
            ->assertSessionHasErrors('receiver_id');

        $this->assertDatabaseCount('messages', 0);
    }

    /* ---------------------------------------------------------------------
     | Routing
     * ------------------------------------------------------------------ */

    public function test_each_role_lands_on_its_own_dashboard(): void
    {
        $this->actingAs($this->admin)->get('/')->assertRedirect('/admin/dashboard');
        $this->actingAs($this->sptLecturer)->get('/')->assertRedirect('/lecturer/dashboard');
        $this->actingAs($this->sptStudent)->get('/')->assertRedirect('/dashboard');
    }

    public function test_an_admin_is_not_bounced_into_a_redirect_loop(): void
    {
        // Previously: admin -> /dashboard -> StudentMiddleware -> /login ->
        // guest middleware -> /dashboard -> ... until the browser gave up.
        $this->actingAs($this->admin)
            ->get('/dashboard')
            ->assertRedirect('/admin/dashboard');

        $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();
    }

    public function test_non_admins_cannot_reach_the_admin_area(): void
    {
        foreach (['/admin/dashboard', '/admin/users', '/admin/courses'] as $url) {
            $this->actingAs($this->sptLecturer)->get($url)->assertRedirect('/lecturer/dashboard');
            $this->actingAs($this->sptStudent)->get($url)->assertRedirect('/dashboard');
        }
    }

    public function test_admin_sees_every_departments_modules(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/courses')
            ->assertOk()
            ->assertSee('SPT101')
            ->assertSee('PE101');
    }

    /* ---------------------------------------------------------------------
     | Regression: the SPT side still works
     * ------------------------------------------------------------------ */

    public function test_existing_screens_still_render_for_each_role(): void
    {
        $this->actingAs($this->sptStudent)->get('/dashboard')->assertOk();
        $this->actingAs($this->sptStudent)->get('/courses')->assertOk();
        $this->actingAs($this->sptStudent)->get('/inbox')->assertOk();
        $this->actingAs($this->sptLecturer)->get('/lecturer/dashboard')->assertOk();
        $this->actingAs($this->sptLecturer)->get('/lecturer/courses')->assertOk();
        $this->actingAs($this->sptLecturer)->get("/lecturer/courses/{$this->sptCourse->id}")->assertOk();
    }
}
