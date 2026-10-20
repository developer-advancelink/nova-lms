<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Lecturer;
use App\Http\Controllers\Student;

/*
|--------------------------------------------------------------------------
| Root redirect
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    // homePath() covers every role. The previous two-way ternary sent admins
    // to /dashboard, which StudentMiddleware then bounced.
    return redirect(Auth::user()->homePath());
});

/*
|--------------------------------------------------------------------------
| Role-neutral landing route
|--------------------------------------------------------------------------
| Named 'dashboard' because six Breeze controllers call route('dashboard'),
| which previously threw RouteNotFoundException — no route had that name.
| Laravel's `guest` middleware also falls back to a route NAMED dashboard
| before matching one by URI, so this is what stops an authenticated admin
| ping-ponging between /login and /dashboard.
*/
Route::get('/home', fn () => redirect(Auth::user()->homePath()))
    ->middleware('auth')
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'student'])->group(function () {

    Route::get('/dashboard', Student\DashboardController::class)->name('student.dashboard');

    Route::get('/courses', [Student\CourseController::class, 'index']);
    Route::post('/courses/{id}/star', [Student\CourseController::class, 'star']);

    // {course} is resolved by Course::resolveRouteBinding(), so a module in
    // another department — or one in this department the student is not
    // enrolled in — 404s before the controller runs.
    Route::get('/courses/{course}', [Student\CourseController::class, 'show']);

    Route::get('/calendar', Student\CalendarController::class);

    Route::get('/inbox', Student\InboxController::class);

    Route::get('/help', HelpController::class);
});

/*
|--------------------------------------------------------------------------
| Lecturer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->group(function () {

    Route::get('/dashboard', Lecturer\DashboardController::class)->name('lecturer.dashboard');

    Route::get('/courses', [Lecturer\CourseController::class, 'index']);

    /*
    |----------------------------------------------------------------------
    | Module routes — scoped bindings
    |----------------------------------------------------------------------
    | {course} resolves through Course::resolveRouteBinding(), so a module the
    | lecturer does not own (or that belongs to the other department) 404s.
    | scopeBindings() then resolves {material} / {assignment} THROUGH the
    | course's own relation, which is what makes it impossible to touch a child
    | record belonging to a different module.
    */
    Route::scopeBindings()->group(function () {

        Route::get('/courses/{course}', [Lecturer\CourseController::class, 'show'])
            ->can('view', 'course');

        Route::patch('/courses/{course}', [Lecturer\CourseController::class, 'update'])
            ->can('update', 'course');

        Route::post('/courses/{course}/materials', [Lecturer\MaterialController::class, 'store'])
            ->can('update', 'course');

        Route::delete('/courses/{course}/materials/{material}', [Lecturer\MaterialController::class, 'destroy'])
            ->can('update', 'course');

        Route::post('/courses/{course}/assignments', [Lecturer\AssignmentController::class, 'store'])
            ->can('update', 'course');

        Route::post('/courses/{course}/announcements', [Lecturer\AnnouncementController::class, 'store'])
            ->can('update', 'course');

        // {studentId} stays a plain id on purpose — see GradeController.
        Route::post('/courses/{course}/assignments/{assignment}/grade/{studentId}', [Lecturer\GradeController::class, 'store'])
            ->can('update', 'course');
    });

    Route::get('/inbox', Lecturer\InboxController::class);

    Route::get('/calendar', Lecturer\CalendarController::class);

    Route::get('/help', HelpController::class);
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| The only area that crosses departments. Course::scopeVisibleTo() returns
| everything for an admin, so {course} bindings here need no special casing.
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Account creation and user management.
        Route::resource('users', Admin\UserController::class)->except(['show']);

        Route::post('users/{user}/password', [Admin\UserController::class, 'resetPassword'])
            ->name('users.password');

        // Module management across both departments.
        Route::resource('courses', Admin\CourseController::class);

        // Roster management. scopeBindings() resolves {student} through
        // Course::students(), so removing someone who is not enrolled 404s.
        Route::scopeBindings()->group(function () {
            Route::post('courses/{course}/students', [Admin\EnrollmentController::class, 'store'])
                ->name('courses.students.store');

            Route::delete('courses/{course}/students/{student}', [Admin\EnrollmentController::class, 'destroy'])
                ->name('courses.students.destroy');
        });
    });

/*
|--------------------------------------------------------------------------
| Own account — all roles
|--------------------------------------------------------------------------
| Student, lecturer and admin each had their own near-identical pair of
| profile/password closures, differing only in the view returned. The view
| name is derivable from the role, so one controller serves all three; the
| paths stay distinct because each sits behind its own role middleware.
*/
foreach ([
    // hasInbox: admin has no inbox screen, so it gets no message endpoints.
    ['prefix' => '', 'middleware' => ['auth', 'student'], 'name' => 'student.', 'hasInbox' => true],
    ['prefix' => 'lecturer', 'middleware' => ['auth', 'lecturer'], 'name' => 'lecturer.', 'hasInbox' => true],
    ['prefix' => 'admin', 'middleware' => ['auth', 'admin'], 'name' => 'admin.', 'hasInbox' => false],
] as $area) {
    Route::middleware($area['middleware'])
        ->prefix($area['prefix'])
        ->name($area['name'])
        ->group(function () use ($area) {
            Route::get('/account', [AccountController::class, 'edit'])->name('account');
            Route::patch('/account', [AccountController::class, 'update']);

            Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password');
            Route::patch('/account/password', [AccountController::class, 'updatePassword']);

            if (! $area['hasInbox']) {
                return;
            }

            // Sending, editing and deleting messages is identical for the two
            // roles that have an inbox; only reading it differs.
            Route::post('/inbox/reply', [MessageController::class, 'reply']);
            Route::patch('/inbox/messages/{id}', [MessageController::class, 'update']);
            Route::delete('/inbox/messages/{id}', [MessageController::class, 'destroy']);
        });
}

require __DIR__.'/auth.php';
