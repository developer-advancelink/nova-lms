<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'lecturer') {
            if (Auth::check() && Auth::user()->role === 'student') {
                return redirect('/dashboard');
            }
            return redirect('/login');
        }

        return $next($request);
    }
}