<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->isStudent()) {
            // Previously this sent any non-student that wasn't a lecturer to
            // /login while still authenticated, and `guest` sent them straight
            // back — an admin could never load a page. homePath() covers every
            // role, so there is no fall-through case now.
            $target = $user->homePath();

            abort_if($request->is(ltrim($target, '/')), 403);

            return redirect($target);
        }

        return $next($request);
    }
}
