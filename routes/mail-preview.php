<?php

use App\Mail\CredencialesContratistaMail;
use App\Mail\RolActualizadoContratistaMail;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Previsualización de Mailables — SOLO entorno local
|--------------------------------------------------------------------------
| Renderiza el HTML final en el navegador sin enviar nada (Mailable
| implementa Renderable desde Laravel 12). Útil para iterar rápido sobre
| la plantilla; para probar el flujo completo (Job -> Mailer -> SMTP),
| usa Mailpit en su lugar.
|
| Incluir condicionalmente desde routes/web.php:
|
|   if (app()->environment('local')) {
|       require __DIR__.'/mail-preview.php';
|   }
|
| No usa datos reales de la BD — el User se instancia en memoria, sin
| guardar, para que el preview funcione incluso en una BD vacía.
*/

Route::get('/mail-preview/credenciales-contratista', function () {
    $user = new User([
        'first_name' => 'Juan',
        'last_name'  => 'Pérez',
        'email'      => 'contratista.prueba@example.com',
    ]);

    return new CredencialesContratistaMail($user, 'Ab12Cd34Ef56');
});

Route::get('/mail-preview/contratista-rol-actualizado', function () {
    $user = new User([
        'first_name' => 'Juan',
        'last_name'  => 'Pérez',
        'email'      => 'contratista.prueba@example.com',
    ]);

    return new RolActualizadoContratistaMail($user);
});
