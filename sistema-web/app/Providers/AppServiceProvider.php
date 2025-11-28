<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Forzar URL base HARDCODEADA para subcarpetas
        URL::forceRootUrl('https://mail.tecnoweb.org.bo/inf513/grupo09sa/churrasquera/sistema-web/public');
        URL::forceScheme('https');
    }
}
