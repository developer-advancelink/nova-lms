<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Admin is the one role that crosses departments. Returning null (not
        // false) for everyone else is essential — false would deny outright and
        // the policies would never run.
        Gate::before(fn (User $user) => $user->isAdmin() ? true : null);
    }
}
