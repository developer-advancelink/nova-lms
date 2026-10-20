<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Schedule;
use App\Models\Submission;
use App\Services\CourseDirectory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CourseDirectory $courses): View
    {
        $user = $request->user();

        $courseList = $courses->visibleTo($user, ['students']);
        $courseIds = $courseList->pluck('id');

        $pendingSubmissions = Submission::whereHas('assignment', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->whereNull('grade')
            ->with(['user', 'assignment.course'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // Schedules and upcoming deadlines are normalised to the same shape so
        // the view can render one combined "coming up" list.
        $schedules = Schedule::whereIn('course_id', $courseIds)
            ->with('course')
            ->get()
            ->map(fn ($s) => (object) [
                'title'    => $s->course->code.' — '.$s->title,
                'subtitle' => $s->day.', '.Carbon::parse($s->time)->format('g:i A'),
            ]);

        $upcomingAssignments = Assignment::whereIn('course_id', $courseIds)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->with('course')
            ->take(3)
            ->get()
            ->map(fn ($a) => (object) [
                'title'    => $a->title.' Deadline',
                'subtitle' => $a->due_date->format('D, g:i A'),
            ]);

        $recentActivity = Submission::whereHas('assignment', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->with(['user', 'assignment.course'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('pages.lecturer.dashboard.index', [
            'courses' => $courseList,
            'pendingSubmissions' => $pendingSubmissions,
            'upcoming' => $schedules->concat($upcomingAssignments)->take(5),
            'recentActivity' => $recentActivity,
        ]);
    }
}
