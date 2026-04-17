<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GenerarDocumentoNominacion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GenerarDocumentoNominacion::class, function ($app) {
            return new GenerarDocumentoNominacion();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
