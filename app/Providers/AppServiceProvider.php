<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\SidebarComposer;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('partials.student.sidebar', SidebarComposer::class);
    }
}