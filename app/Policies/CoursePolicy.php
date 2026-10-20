<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

/**
 * What a user may DO to a module they can already reach.
 *
 * Reachability is handled earlier, by Course::resolveRouteBinding() — a module
 * outside the user's department never gets this far. This policy is the second
 * layer: it decides capability, and it is what `->can('update', 'course')` in
 * the route file consults.
 *
 * Admins never reach these methods: Gate::before() in AppServiceProvider
 * short-circuits for them.
 *
 * Auto-discovered by Laravel (App\Models\Course -> App\Policies\CoursePolicy),
 * so there is nothing to register.
 */
class CoursePolicy
{
    /**
     * Anyone signed in may see their own module list; which modules are in it
     * is decided by scopeVisibleTo(), not here.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->department !== $course->department) {
            return false;
        }

        return $user->isLecturer()
            ? $course->lecturer_id === $user->id
            : $course->enrollments()->where('user_id', $user->id)->exists();
    }

    /**
     * Modifying a module — renaming it, uploading materials, posting
     * announcements, creating assignments, grading. Owning lecturers only.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->isLecturer()
            && $course->lecturer_id === $user->id
            && $user->department === $course->department;
    }

    /**
     * Deleting a module is an admin action only, so this returns false for
     * everyone and Gate::before() lets admins through.
     */
    public function delete(User $user, Course $course): bool
    {
        return false;
    }
}
