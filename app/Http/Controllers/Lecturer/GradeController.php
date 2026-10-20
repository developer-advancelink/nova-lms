<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * The route is /courses/{course}/assignments/{assignment}/grade/{studentId}.
     *
     * {studentId} is deliberately a plain id, NOT a User type-hint. Under the
     * group's scopeBindings() each parameter resolves through the PREVIOUS one,
     * so a User hint would make Laravel look for Assignment::students(), which
     * does not exist. Resolving it through the course's own roster instead both
     * finds the student and authorises them in one step — this is the only
     * hand-written ownership check in the app.
     */
    public function store(Request $request, Course $course, Assignment $assignment, $studentId): RedirectResponse
    {
        $student = $course->students()->find($studentId);

        abort_unless($student, 403);

        $request->validate([
            'grade'    => ['nullable', 'string', 'max:10'],
            'feedback' => ['nullable', 'string'],
        ]);

        Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $student->id],
            ['grade' => $request->input('grade'), 'feedback' => $request->input('feedback')]
        );

        return redirect()->back()->with('success', 'Grade saved.');
    }
}
