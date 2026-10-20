<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\Submission;
use App\Services\CourseDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CourseDirectory $courses): View
    {
        $user = $request->user();

        // Every list below is derived from these ids, so they all inherit the
        // department and enrollment scoping.
        $enrolledCourses = $courses->visibleTo($user);
        $courseIds = $enrolledCourses->pluck('id');

        $pendingAssignments = Assignment::whereIn('course_id', $courseIds)
            ->whereDoesntHave('submissions', fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('due_date')
            ->with('course')
            ->get();

        $schedules = Schedule::whereIn('course_id', $courseIds)->with('course')->get();

        $recentFeedback = Submission::where('user_id', $user->id)
            ->whereNotNull('feedback')
            ->with('assignment')
            ->latest('submitted_at')
            ->take(3)
            ->get();

        return view('pages.student.dashboard.index', [
            'courses' => $enrolledCourses,
            'enrolledCourses' => $enrolledCourses,
            'pendingAssignments' => $pendingAssignments,
            'schedules' => $schedules,
            'recentFeedback' => $recentFeedback,
        ]);
    }
}
