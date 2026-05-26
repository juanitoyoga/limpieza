<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Crea la respuesta HTTP al login exitoso.
     *
     * Fortify por defecto usa $request->wantsJson() para decidir si retorna
     * JSON o redirige. Esto causa que algunos navegadores reciban JSON.
     *
     * Esta implementación fuerza la redirección para peticiones web,
     * y solo retorna JSON si la petición viene explícitamente de /api/*.
     */
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        // Peticiones de la API móvil → JSON
        // (estas llegan a /api/auth/login, no a /login, pero por si acaso)
        if ($request->is('api/*')) {
            return response()->json([
                'two_factor' => false,
            ]);
        }

        // Peticiones web → siempre redirigir, nunca JSON
        return redirect()->intended(config('fortify.home', '/info'));
    }
}
