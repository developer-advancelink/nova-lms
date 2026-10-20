<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\CourseDirectory;
use App\Services\InboxService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function __invoke(Request $request, InboxService $inbox, CourseDirectory $courses): View
    {
        $user = $request->user();
        $enrolledCourses = $courses->visibleTo($user);

        return view('pages.student.inbox.index', array_merge(
            $inbox->threadData($user, $request->query('thread')),
            [
                'enrolledCourses' => $enrolledCourses,
                'inboxCourses' => $enrolledCourses,
                // Students message the lecturers teaching their own modules.
                'recipients' => $inbox->recipientsFor($user),
            ]
        ));
    }
}
