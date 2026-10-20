<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string'],
            'body'  => ['required', 'string'],
        ]);

        $course->announcements()->create([
            'posted_by' => $request->user()->id,
            'title'     => $request->input('title'),
            'body'      => $request->input('body'),
        ]);

        return redirect()->back()->with('success', 'Announcement posted.');
    }
}
