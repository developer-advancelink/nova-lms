<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'title'       => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'due_date'    => ['required', 'date'],
            'file'        => ['nullable', 'file', 'mimes:pdf,docx,pptx', 'max:10240'],
        ]);

        $course->assignments()->create($request->only('title', 'description', 'due_date'));

        return redirect()->back()->with('success', 'Assignment created successfully.');
    }
}
