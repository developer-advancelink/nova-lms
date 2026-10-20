<?php

namespace App\Http\Controllers\Lecturer;

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
        $courseList = $courses->visibleTo($user, ['students']);

        return view('pages.lecturer.inbox.index', array_merge(
            $inbox->threadData($user, $request->query('thread')),
            [
                'courses' => $courseList,
                'inboxCourses' => $courseList,
                // Lecturers message the students enrolled in their own modules.
                'recipients' => $inbox->recipientsFor($user),
            ]
        ));
    }
}
