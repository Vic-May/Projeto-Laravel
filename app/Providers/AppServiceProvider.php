<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // A paginação padrão do Laravel usa Tailwind; o projeto usa Bootstrap 5.
        Paginator::useBootstrapFive();
    }
}
