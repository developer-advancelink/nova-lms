<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Enrollment;

class SidebarComposer
{
    public function compose(View $view): void
    {
        if (!Auth::check()) return;

        $enrolledCourses = Enrollment::where('user_id', Auth::id())
            ->with('course')
            ->get()
            ->pluck('course');

        $view->with('enrolledCourses', $enrolledCourses);
    }
}