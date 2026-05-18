<?php

namespace App\Providers;

use App\View\Composers\TopnavbarBreadcrumbsComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.theme.partials.topnavbar', TopnavbarBreadcrumbsComposer::class);
    }
}
