<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GenerarDocumentoNominacion;

// --- Agrega estos dos use ---
use Laravel\Fortify\Contracts\LoginResponse;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Tu singleton existente — no lo tocas
        $this->app->singleton(GenerarDocumentoNominacion::class, function ($app) {
            return new GenerarDocumentoNominacion();
        });

        // Nuevo: sobreescribir la respuesta de login de Fortify
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $user  = $request->user();
                    $token = $user->createToken('mobile')->plainTextToken;

                    return response()->json([
                        'user'  => $user,
                        'token' => $token,
                    ]);
                }
            };
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
