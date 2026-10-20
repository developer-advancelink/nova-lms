<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The one place that turns "a user" into "the modules they may see".
 *
 * This replaces the $enrolledCoursesForUser and $lecturerCoursesForUser
 * closures that used to be assigned to variables in routes/web.php and
 * `use`-injected into the route groups.
 *
 * Every method here goes through Course::visibleTo(), which enforces
 * department AND ownership/enrollment — read the docblock at the top of
 * App\Models\Course before adding one. Around two dozen call sites derive
 * course ids from this class, so they all inherit the scoping.
 */
class CourseDirectory
{
    /**
     * The modules a user may see, in teaching order.
     *
     * Queries Course directly rather than plucking through Enrollment: an
     * enrollment row that outlives its course would otherwise leave a null in
     * the collection and fatal in partials/sidebar.blade.php.
     *
     * @param  array<int, string>  $with  relations to eager load
     */
    public function visibleTo(?User $user, array $with = []): Collection
    {
        return $this->scoped($user)
            ->with($with)
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Ids only, for the whereIn('course_id', ...) lookups that hang off a
     * module list, and for the Rule::in() allow-lists in the form requests.
     */
    public function visibleIds(?User $user): Collection
    {
        return $this->scoped($user)->pluck('id');
    }

    /**
     * The lecturers teaching a user's visible modules — who a student is
     * allowed to message.
     */
    public function visibleLecturerIds(?User $user): Collection
    {
        return $this->scoped($user)
            ->pluck('lecturer_id')
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * The scoped query itself, for subqueries and for callers that need to
     * compose their own eager loads or ordering. Exposed so that nothing has
     * to reach for Course::query() and lose the scope.
     */
    public function scoped(?User $user): Builder
    {
        return Course::visibleTo($user);
    }
}
