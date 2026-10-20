<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Services\CourseDirectory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request, CourseDirectory $courses): View
    {
        $currentMonth = $request->filled('month')
            ? Carbon::createFromFormat('Y-m', $request->input('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $enrolledCourses = $courses->visibleTo($request->user());
        $courseIds = $enrolledCourses->pluck('id');

        $events = Assignment::whereIn('course_id', $courseIds)
            ->whereBetween('due_date', [
                $currentMonth->copy()->startOfMonth(),
                $currentMonth->copy()->endOfMonth(),
            ])
            ->with('course')
            ->get();

        $upcoming = Assignment::whereIn('course_id', $courseIds)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->with('course')
            ->take(5)
            ->get();

        return view('pages.student.calendar.index', compact(
            'currentMonth', 'events', 'upcoming', 'enrolledCourses'
        ));
    }
}
