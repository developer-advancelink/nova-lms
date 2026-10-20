<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Services\CourseDirectory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private CourseDirectory $courses)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        // Constrained to visible courses so a stale or cross-department
        // enrollment row can never surface a module here.
        $enrollments = Enrollment::where('user_id', $user->id)
            ->whereIn('course_id', $this->courses->scoped($user)->select('id'))
            ->with('course.lecturer')
            ->orderByDesc('starred')
            ->orderBy('created_at')
            ->get();

        return view('pages.student.courses.index', [
            'enrollments' => $enrollments,
            'enrolledCourses' => $this->courses->visibleTo($user),
        ]);
    }

    /**
     * {course} is resolved by Course::resolveRouteBinding(), so a module in
     * another department — or one in this department the student is not
     * enrolled in — 404s before this method runs.
     */
    public function show(Request $request, Course $course): View
    {
        $user = $request->user();

        $course->load(['lecturer', 'materials', 'assignments', 'announcements.poster', 'students']);

        $submissions = Submission::where('user_id', $user->id)
            ->whereHas('assignment', fn ($q) => $q->where('course_id', $course->id))
            ->with('assignment')
            ->get();

        return view('pages.student.courses.show', [
            'course' => $course,
            'submissions' => $submissions,
            'enrolledCourses' => $this->courses->visibleTo($user),
        ]);
    }

    /**
     * Toggle the star on an enrollment. Scoped by user_id and course_id
     * together, so it fails closed for a module the student is not enrolled in
     * without needing the route binding.
     */
    public function star(Request $request, $id): JsonResponse
    {
        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $id)
            ->firstOrFail();

        $enrollment->update(['starred' => ! $enrollment->starred]);

        return response()->json(['starred' => $enrollment->starred]);
    }
}
