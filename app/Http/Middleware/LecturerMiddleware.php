<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LecturerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->isLecturer()) {
            $target = $user->homePath();

            abort_if($request->is(ltrim($target, '/')), 403);

            return redirect($target);
        }

        return $next($request);
    }
}
