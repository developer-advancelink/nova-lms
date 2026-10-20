<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string'],
            'file'  => ['required', 'file', 'mimes:pdf,docx,pptx', 'max:10240'],
            'type'  => ['nullable', 'string'],
            'week'  => ['nullable', 'string'],
        ]);

        $course->materials()->create([
            'title'    => $request->input('title'),
            'type'     => $request->input('type', 'notes'),
            'week'     => $request->input('week'),
            'file_url' => $request->file('file')->store('materials', 'public'),
        ]);

        return redirect()->back()->with('success', 'Module uploaded successfully.');
    }

    /**
     * {material} is resolved as $course->materials()->find(...) by the route
     * group's scopeBindings(), so the course segment is load-bearing. Before
     * that it was accepted and ignored entirely, which let any lecturer delete
     * any material in the system by passing their own course id.
     */
    public function destroy(Course $course, CourseMaterial $material): RedirectResponse
    {
        if ($material->file_url) {
            Storage::disk('public')->delete($material->file_url);
        }

        $material->delete();

        return redirect()->back()->with('success', 'Module deleted.');
    }
}
