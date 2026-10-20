<?php

namespace App\Http\Controllers;

use App\Services\CourseDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Both roles render the same pages.shared.help view; only the sidebar's course
 * list differs, and the layout reads whichever variable its role branch wants.
 */
class HelpController extends Controller
{
    public function __invoke(Request $request, CourseDirectory $courses): View
    {
        $list = $courses->visibleTo($request->user());

        return view('pages.shared.help', [
            'courses' => $list,
            'enrolledCourses' => $list,
        ]);
    }
}
