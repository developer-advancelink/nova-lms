<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Department;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Admin is the only role that sees across departments, so these counts
        // are deliberately unscoped by department.
        $departments = collect(Department::cases())->map(fn (Department $d) => [
            'department' => $d,
            'lecturers'  => User::where('role', 'lecturer')->where('department', $d->value)->count(),
            'students'   => User::where('role', 'student')->where('department', $d->value)->count(),
            'modules'    => Course::where('department', $d->value)->count(),
        ]);

        // Accounts that can see nothing until an admin assigns them a
        // department — surfaced here so they are fixable rather than invisible.
        $unassigned = User::whereNull('department')
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();

        $totals = [
            'users'   => User::count(),
            'admins'  => User::where('role', 'admin')->count(),
            'modules' => Course::count(),
        ];

        return view('pages.admin.dashboard.index', compact('departments', 'unassigned', 'totals'));
    }
}
