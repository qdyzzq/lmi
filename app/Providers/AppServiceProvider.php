<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use App\Http\Controllers\Sprs\Composers\SidebarComposer;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('sprs.layouts.layout', SidebarComposer::class);
    }
}