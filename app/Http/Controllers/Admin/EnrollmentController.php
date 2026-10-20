<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        // A cross-department enrollment is the single row that can make the
        // whole visibility model incoherent — the student would hold an
        // enrollment for a module scopeVisibleTo() will never show them. So
        // the student must be in this module's department.
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($q) use ($course) {
                    $q->where('role', 'student')
                      ->where('department', $course->department);
                }),
            ],
        ], [
            'user_id.exists' => 'That student is not in this module\'s department.',
        ]);

        Enrollment::firstOrCreate([
            'user_id'   => $request->input('user_id'),
            'course_id' => $course->id,
        ]);

        return back()->with('success', 'Student enrolled.');
    }

    public function destroy(Course $course, User $student): RedirectResponse
    {
        Enrollment::where('course_id', $course->id)
            ->where('user_id', $student->id)
            ->delete();

        return back()->with('success', "{$student->name} removed from {$course->code}.");
    }
}
