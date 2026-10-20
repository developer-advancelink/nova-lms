<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            // ->guest() remembers the intended URL and uses the standard
            // unauthenticated path. Never redirect an *authenticated* user to
            // /login: that route is behind `guest`, which bounces them back.
            return redirect()->guest(route('login'));
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->isAdmin()) {
            $target = $user->homePath();

            // Loop breaker: if this user's own home path is somehow guarded by
            // this middleware, 403 rather than redirecting forever.
            abort_if($request->is(ltrim($target, '/')), 403);

            return redirect($target);
        }

        return $next($request);
    }
}
