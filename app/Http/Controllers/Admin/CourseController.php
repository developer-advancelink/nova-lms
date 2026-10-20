<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Department;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        // visibleTo() is unconstrained for an admin, so using it here keeps
        // every course query in the app going through the same predicate.
        $courses = Course::visibleTo($request->user())
            ->when($request->filled('department'), fn ($q) => $q->where('department', $request->input('department')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->input('q');
                $q->where(fn ($inner) => $inner
                    ->where('code', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%"));
            })
            ->with('lecturer')
            ->withCount('enrollments')
            ->orderBy('department')
            ->orderBy('code')
            ->paginate(25)
            ->withQueryString();

        return view('pages.admin.courses.index', [
            'courses'     => $courses,
            'departments' => Department::cases(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.courses.create', [
            'departments' => Department::cases(),
            'lecturers'   => $this->lecturersByDepartment(),
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = Course::create($request->validated());

        return redirect()
            ->route('admin.courses.index')
            ->with('success', "Module {$course->code} created.");
    }

    public function show(Course $course): View
    {
        $course->load(['lecturer', 'students', 'assignments', 'materials']);

        // Candidates for enrollment: students in this module's department who
        // are not already on the roster. Scoping the dropdown is what keeps a
        // cross-department enrollment from being offered in the first place.
        $enrollable = User::where('role', 'student')
            ->where('department', $course->department)
            ->whereDoesntHave('enrollments', fn ($q) => $q->where('course_id', $course->id))
            ->orderBy('name')
            ->get();

        return view('pages.admin.courses.show', compact('course', 'enrollable'));
    }

    public function edit(Course $course): View
    {
        return view('pages.admin.courses.edit', [
            'course'      => $course,
            'departments' => Department::cases(),
            'lecturers'   => $this->lecturersByDepartment(),
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()
            ->route('admin.courses.index')
            ->with('success', "Module {$course->code} updated.");
    }

    public function destroy(Course $course): RedirectResponse
    {
        // Every child table is ON DELETE CASCADE, so make the blast radius
        // explicit rather than discovering it afterwards.
        if ($course->enrollments()->exists()) {
            return back()->withErrors([
                'course' => "{$course->code} still has {$course->enrollments()->count()} enrolled student(s). Remove them before deleting the module.",
            ]);
        }

        $code = $course->code;
        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', "Module {$code} deleted.");
    }

    /**
     * Lecturers grouped by department, so the create/edit forms can filter the
     * dropdown client-side as soon as a department is chosen.
     *
     * @return array<string, \Illuminate\Support\Collection>
     */
    private function lecturersByDepartment(): array
    {
        $lecturers = User::where('role', 'lecturer')
            ->whereNotNull('department')
            ->orderBy('name')
            ->get(['id', 'name', 'department']);

        $grouped = [];

        foreach (Department::cases() as $department) {
            $grouped[$department->value] = $lecturers
                ->where('department', $department->value)
                ->values();
        }

        return $grouped;
    }
}
