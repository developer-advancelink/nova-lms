<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lecturer\UpdateCourseRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Services\CourseDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(private CourseDirectory $courses)
    {
    }

    public function index(Request $request): View
    {
        $courseList = $this->courses->visibleTo($request->user(), ['students', 'assignments.submissions']);

        $assignments = Assignment::whereIn('course_id', $courseList->pluck('id'))
            ->with(['course.students', 'submissions'])
            ->orderBy('due_date')
            ->get();

        return view('pages.lecturer.courses.index', [
            'courses' => $courseList,
            'assignments' => $assignments,
        ]);
    }

    public function show(Request $request, Course $course): View
    {
        $course->load(['lecturer', 'materials', 'assignments.submissions', 'announcements', 'students']);

        return view('pages.lecturer.courses.show', [
            'course' => $course,
            'courses' => $this->courses->visibleTo($request->user(), ['students']),
        ]);
    }

    /**
     * `description` works now — the column was added alongside the department
     * work. This form has always posted it to a column that did not exist.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->back()->with('success', 'Course updated successfully.');
    }
}
