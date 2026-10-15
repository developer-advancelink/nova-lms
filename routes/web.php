<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\LecturerMiddleware;

/*
|--------------------------------------------------------------------------
| Root redirect
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (!Auth::check()) return redirect('/login');
    return Auth::user()->role === 'lecturer'
        ? redirect('/lecturer/dashboard')
        : redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| Inbox data helper
|--------------------------------------------------------------------------
*/
$inboxData = function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $threads = \App\Models\Message::threads()
        ->where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver', 'course', 'replies.sender'])
        ->orderByDesc('created_at')
        ->get();

    $activeThread = null;
    if (request()->has('thread')) {
        $activeThread = \App\Models\Message::with(['sender', 'receiver', 'course', 'replies.sender'])
            ->find(request('thread'));

        if ($activeThread && $activeThread->receiver_id === $user->id && is_null($activeThread->read_at)) {
            $activeThread->update(['read_at' => now()]);
        }
    }

    return compact('threads', 'activeThread');
};

/*
|--------------------------------------------------------------------------
| Sidebar course helpers
|--------------------------------------------------------------------------
*/
$enrolledCoursesForUser = function () {
    return \App\Models\Enrollment::where('user_id', Auth::id())
        ->with('course')
        ->get()
        ->pluck('course');
};

$lecturerCoursesForUser = function () {
    return \App\Models\Course::where('lecturer_id', Auth::id())
        ->with('students')
        ->orderBy('start_date')
        ->get();
};

/*
|--------------------------------------------------------------------------
| Shared password validation rules
|--------------------------------------------------------------------------
*/
$passwordRules = function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    return [
        'required',
        'min:12',
        'confirmed',
        'regex:/[A-Z]/',
        'regex:/[a-z]/',
        'regex:/[0-9]/',
        'regex:/[^A-Za-z0-9]/',
        function ($attribute, $value, $fail) use ($user) {
            $nameParts = array_filter(explode(' ', strtolower($user->name)));
            foreach ($nameParts as $part) {
                if (strlen($part) > 2 && str_contains(strtolower($value), $part)) {
                    $fail('Password must not contain your name.');
                    return;
                }
            }
        },
    ];
};

$passwordMessages = [
    'password.min'       => 'Password must be at least 12 characters.',
    'password.regex'     => 'Password must include uppercase, lowercase, a number, and a symbol.',
    'password.confirmed' => 'Passwords do not match.',
];

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware([StudentMiddleware::class])->group(function () use ($inboxData, $enrolledCoursesForUser, $passwordRules, $passwordMessages) {

    Route::get('/dashboard', function () use ($enrolledCoursesForUser) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $enrolledCourses = $enrolledCoursesForUser();
        $courseIds = $enrolledCourses->pluck('id');

        $pendingAssignments = \App\Models\Assignment::whereIn('course_id', $courseIds)
            ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('due_date')
            ->with('course')
            ->get();

        $schedules = \App\Models\Schedule::whereIn('course_id', $courseIds)->with('course')->get();

        $recentFeedback = \App\Models\Submission::where('user_id', $user->id)
            ->whereNotNull('feedback')
            ->with('assignment')
            ->latest('submitted_at')
            ->take(3)
            ->get();

        $courses = $enrolledCourses;

        return view('pages.student.dashboard.index', compact(
            'courses', 'enrolledCourses', 'pendingAssignments', 'schedules', 'recentFeedback'
        ));
    })->name('student.dashboard');

    Route::get('/courses', function () use ($enrolledCoursesForUser) {
        $enrollments = \App\Models\Enrollment::where('user_id', Auth::id())
            ->with('course.lecturer')
            ->orderByDesc('starred')
            ->orderBy('created_at')
            ->get();
        $enrolledCourses = $enrolledCoursesForUser();
        return view('pages.student.courses.index', compact('enrollments', 'enrolledCourses'));
    });

    Route::post('/courses/{id}/star', function ($id) {
        $enrollment = \App\Models\Enrollment::where('user_id', Auth::id())
            ->where('course_id', $id)
            ->firstOrFail();
        $enrollment->update(['starred' => !$enrollment->starred]);
        return response()->json(['starred' => $enrollment->starred]);
    });

    Route::get('/courses/{id}', function ($id) use ($enrolledCoursesForUser) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $course = \App\Models\Course::with([
            'lecturer', 'materials', 'assignments', 'announcements.poster', 'students',
        ])->findOrFail($id);

        $submissions = \App\Models\Submission::where('user_id', $user->id)
            ->whereHas('assignment', fn($q) => $q->where('course_id', $id))
            ->with('assignment')
            ->get();

        $enrolledCourses = $enrolledCoursesForUser();
        return view('pages.student.courses.show', compact('course', 'submissions', 'enrolledCourses'));
    });

    Route::get('/calendar', function () use ($enrolledCoursesForUser) {
        $currentMonth = request('month')
            ? \Carbon\Carbon::createFromFormat('Y-m', request('month'))->startOfMonth()
            : \Carbon\Carbon::now()->startOfMonth();

        $enrolledCourses = $enrolledCoursesForUser();
        $courseIds = $enrolledCourses->pluck('id');

        $events = \App\Models\Assignment::whereIn('course_id', $courseIds)
            ->whereBetween('due_date', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()])
            ->with('course')->get();

        $upcoming = \App\Models\Assignment::whereIn('course_id', $courseIds)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->with('course')
            ->take(5)
            ->get();

        return view('pages.student.calendar.index', compact('currentMonth', 'events', 'upcoming', 'enrolledCourses'));
    });

    Route::get('/inbox', function () use ($inboxData, $enrolledCoursesForUser) {
        $enrolledCourses = $enrolledCoursesForUser();
        $inboxCourses = $enrolledCourses;

        // Students can message their lecturers
        $courseIds = $enrolledCourses->pluck('id');
        $recipients = \App\Models\User::whereIn('id',
            \App\Models\Course::whereIn('id', $courseIds)->pluck('lecturer_id')
        )->get();

        return view('pages.student.inbox.index', array_merge($inboxData(), compact('enrolledCourses', 'recipients', 'inboxCourses')));
    });

    Route::post('/inbox/reply', function () {
        \App\Models\Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => request('receiver_id'),
            'course_id'   => request('course_id') ?: null,
            'parent_id'   => request('parent_id'),
            'subject'     => request('subject'),
            'body'        => request('body'),
        ]);
        return redirect()->back();
    });

    Route::patch('/inbox/messages/{id}', function ($id) {
        $message = \App\Models\Message::where('id', $id)
            ->where('sender_id', Auth::id())
            ->firstOrFail();
        $message->update(['body' => request('body')]);
        return response()->json(['success' => true]);
    });

    Route::delete('/inbox/messages/{id}', function ($id) {
        $message = \App\Models\Message::where('id', $id)
            ->where('sender_id', Auth::id())
            ->firstOrFail();
        $message->delete();
        return response()->json(['success' => true]);
    });

    Route::get('/account', function () use ($enrolledCoursesForUser) {
        $enrolledCourses = $enrolledCoursesForUser();
        $courses = $enrolledCourses;
        return view('pages.student.account.profile', compact('courses', 'enrolledCourses'));
    });

    Route::patch('/account', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'name'  => request('name'),
            'email' => request('email'),
            'bio'   => request('bio'),
        ]);
        return redirect()->back()->with('success', 'Profile updated successfully.');
    });

    Route::get('/account/password', function () use ($enrolledCoursesForUser) {
        $enrolledCourses = $enrolledCoursesForUser();
        return view('pages.student.account.password', compact('enrolledCourses'));
    });

    Route::patch('/account/password', function () use ($passwordRules, $passwordMessages) {
        request()->validate([
            'current_password' => ['required'],
            'password'         => $passwordRules(),
        ], $passwordMessages);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!\Illuminate\Support\Facades\Hash::check(request('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make(request('password'))]);
        return redirect()->back()->with('success', 'Password updated successfully.');
    });

    Route::get('/account/notifications', function () use ($enrolledCoursesForUser) {
        $enrolledCourses = $enrolledCoursesForUser();
        return view('pages.student.account.notifications', compact('enrolledCourses'));
    });

    Route::get('/account/preferences', function () use ($enrolledCoursesForUser) {
        $enrolledCourses = $enrolledCoursesForUser();
        return view('pages.student.account.preferences', compact('enrolledCourses'));
    });

    Route::get('/help', function () use ($enrolledCoursesForUser) {
        $enrolledCourses = $enrolledCoursesForUser();
        return view('pages.shared.help', compact('enrolledCourses'));
    });

});

/*
|--------------------------------------------------------------------------
| Lecturer Routes
|--------------------------------------------------------------------------
*/
Route::middleware([LecturerMiddleware::class])->prefix('lecturer')->group(function () use ($inboxData, $lecturerCoursesForUser, $passwordRules, $passwordMessages) {

    Route::get('/dashboard', function () use ($lecturerCoursesForUser) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $courses = $lecturerCoursesForUser();
        $courseIds = $courses->pluck('id');

        $pendingSubmissions = \App\Models\Submission::whereHas('assignment', fn($q) => $q->whereIn('course_id', $courseIds))
            ->whereNull('grade')
            ->with(['user', 'assignment.course'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $schedules = \App\Models\Schedule::whereIn('course_id', $courseIds)
            ->with('course')
            ->get()
            ->map(fn($s) => (object)[
                'title'    => $s->course->code . ' — ' . $s->title,
                'subtitle' => $s->day . ', ' . \Carbon\Carbon::parse($s->time)->format('g:i A'),
            ]);

        $upcomingAssignments = \App\Models\Assignment::whereIn('course_id', $courseIds)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->with('course')
            ->take(3)
            ->get()
            ->map(fn($a) => (object)[
                'title'    => $a->title . ' Deadline',
                'subtitle' => $a->due_date->format('D, g:i A'),
            ]);

        $upcoming = $schedules->concat($upcomingAssignments)->take(5);

        $recentActivity = \App\Models\Submission::whereHas('assignment', fn($q) => $q->whereIn('course_id', $courseIds))
            ->with(['user', 'assignment.course'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('pages.lecturer.dashboard.index', compact(
            'courses', 'pendingSubmissions', 'upcoming', 'recentActivity'
        ));
    })->name('lecturer.dashboard');

    Route::get('/courses', function () use ($lecturerCoursesForUser) {
        $courses = \App\Models\Course::where('lecturer_id', Auth::id())
            ->with(['students', 'assignments.submissions'])
            ->orderBy('start_date')
            ->get();

        $courseIds = $courses->pluck('id');

        $assignments = \App\Models\Assignment::whereIn('course_id', $courseIds)
            ->with(['course.students', 'submissions'])
            ->orderBy('due_date')
            ->get();

        return view('pages.lecturer.courses.index', compact('courses', 'assignments'));
    });

    Route::patch('/courses/{id}', function ($id) {
        $course = \App\Models\Course::findOrFail($id);
        $course->update([
            'name'        => request('name'),
            'description' => request('description'),
            'start_date'  => request('start_date'),
            'end_date'    => request('end_date'),
        ]);
        return redirect()->back()->with('success', 'Course updated successfully.');
    });

    Route::get('/courses/{id}', function ($id) use ($lecturerCoursesForUser) {
        $course = \App\Models\Course::with([
            'lecturer', 'materials', 'assignments.submissions', 'announcements', 'students',
        ])->findOrFail($id);
        $courses = $lecturerCoursesForUser();
        return view('pages.lecturer.courses.show', compact('course', 'courses'));
    });

    Route::post('/courses/{id}/materials', function ($id) {
        request()->validate([
            'title' => ['required', 'string'],
            'file'  => ['required', 'file', 'mimes:pdf,docx,pptx', 'max:10240'],
        ]);
        $path = request()->file('file')->store('materials', 'public');
        \App\Models\CourseMaterial::create([
            'course_id' => $id,
            'title'     => request('title'),
            'type'      => request('type', 'notes'),
            'week'      => request('week'),
            'file_url'  => $path,
        ]);
        return redirect()->back()->with('success', 'Module uploaded successfully.');
    });

    Route::delete('/courses/{courseId}/materials/{materialId}', function ($courseId, $materialId) {
        $material = \App\Models\CourseMaterial::findOrFail($materialId);
        if ($material->file_url) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_url);
        }
        $material->delete();
        return redirect()->back()->with('success', 'Module deleted.');
    });

    Route::post('/courses/{id}/assignments', function ($id) {
        request()->validate([
            'title'    => ['required', 'string'],
            'due_date' => ['required', 'date'],
            'file'     => ['nullable', 'file', 'mimes:pdf,docx,pptx', 'max:10240'],
        ]);
        \App\Models\Assignment::create([
            'course_id'   => $id,
            'title'       => request('title'),
            'description' => request('description'),
            'due_date'    => request('due_date'),
        ]);
        return redirect()->back()->with('success', 'Assignment created successfully.');
    });

    Route::post('/courses/{id}/announcements', function ($id) {
        request()->validate([
            'title' => ['required', 'string'],
            'body'  => ['required', 'string'],
        ]);
        \App\Models\Announcement::create([
            'course_id' => $id,
            'posted_by' => Auth::id(),
            'title'     => request('title'),
            'body'      => request('body'),
        ]);
        return redirect()->back()->with('success', 'Announcement posted.');
    });

    Route::post('/assignments/{assignmentId}/grade/{studentId}', function ($assignmentId, $studentId) {
        \App\Models\Submission::updateOrCreate(
            ['assignment_id' => $assignmentId, 'user_id' => $studentId],
            ['grade' => request('grade'), 'feedback' => request('feedback')]
        );
        return redirect()->back()->with('success', 'Grade saved.');
    });

    Route::get('/inbox', function () use ($inboxData, $lecturerCoursesForUser) {
        $courses = $lecturerCoursesForUser();
        $inboxCourses = $courses;
        $courseIds = $courses->pluck('id');

        // Lecturers can message their students
        $recipients = \App\Models\User::whereHas('enrollments', function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->where('role', 'student')->get();

        return view('pages.lecturer.inbox.index', array_merge($inboxData(), compact('courses', 'recipients', 'inboxCourses')));
    });

    Route::post('/inbox/reply', function () {
        \App\Models\Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => request('receiver_id'),
            'course_id'   => request('course_id') ?: null,
            'parent_id'   => request('parent_id'),
            'subject'     => request('subject'),
            'body'        => request('body'),
        ]);
        return redirect()->back();
    });

    Route::patch('/inbox/messages/{id}', function ($id) {
        $message = \App\Models\Message::where('id', $id)
            ->where('sender_id', Auth::id())
            ->firstOrFail();
        $message->update(['body' => request('body')]);
        return response()->json(['success' => true]);
    });

    Route::delete('/inbox/messages/{id}', function ($id) {
        $message = \App\Models\Message::where('id', $id)
            ->where('sender_id', Auth::id())
            ->firstOrFail();
        $message->delete();
        return response()->json(['success' => true]);
    });

    Route::get('/account', function () use ($lecturerCoursesForUser) {
        $courses = $lecturerCoursesForUser();
        return view('pages.lecturer.account.profile', compact('courses'));
    });

    Route::patch('/account', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'name'  => request('name'),
            'email' => request('email'),
            'bio'   => request('bio'),
        ]);
        return redirect()->back()->with('success', 'Profile updated successfully.');
    });

    Route::get('/account/password', function () use ($lecturerCoursesForUser) {
        $courses = $lecturerCoursesForUser();
        return view('pages.lecturer.account.password', compact('courses'));
    });

    Route::patch('/account/password', function () use ($passwordRules, $passwordMessages) {
        request()->validate([
            'current_password' => ['required'],
            'password'         => $passwordRules(),
        ], $passwordMessages);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!\Illuminate\Support\Facades\Hash::check(request('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make(request('password'))]);
        return redirect()->back()->with('success', 'Password updated successfully.');
    });

    Route::get('/account/notifications', function () use ($lecturerCoursesForUser) {
        $courses = $lecturerCoursesForUser();
        return view('pages.lecturer.account.notifications', compact('courses'));
    });

    Route::get('/account/preferences', function () use ($lecturerCoursesForUser) {
        $courses = $lecturerCoursesForUser();
        return view('pages.lecturer.account.preferences', compact('courses'));
    });

    Route::get('/calendar', function () use ($lecturerCoursesForUser) {
        $currentMonth = request('month')
            ? \Carbon\Carbon::createFromFormat('Y-m', request('month'))->startOfMonth()
            : \Carbon\Carbon::now()->startOfMonth();

        $courses = $lecturerCoursesForUser();
        $courseIds = $courses->pluck('id');

        $events = \App\Models\Assignment::whereIn('course_id', $courseIds)
            ->whereBetween('due_date', [$currentMonth->copy()->startOfMonth(), $currentMonth->copy()->endOfMonth()])
            ->with('course')->get();

        $upcoming = \App\Models\Assignment::whereIn('course_id', $courseIds)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->with('course')
            ->take(5)
            ->get();

        return view('pages.lecturer.calendar.index', compact('currentMonth', 'events', 'upcoming', 'courses'));
    });

    Route::get('/help', function () use ($lecturerCoursesForUser) {
        $courses = $lecturerCoursesForUser();
        return view('pages.shared.help', compact('courses'));
    });

});

/*
|--------------------------------------------------------------------------
| Breeze Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';