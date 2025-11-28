<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\Contracts\RoleMenuServiceInterface;

use App\Services\RoleMenuService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // Auto-registered by make:repository

        $this->app->bind(RoleMenuServiceInterface::class, RoleMenuService::class);
    }

    /**
     * Bootstrap any application services.
     */

     public function boot()
     {
         
  
     }
}

