@component('mail::message')
# Tu cuenta ahora es Contratista

Hola **{{ $user->first_name }}**,

Tu cuenta en LimpiaTuRincón ({{ $user->email }}) fue habilitada como **Contratista**. Ya puedes iniciar sesión con tu contraseña actual — no es necesario que la cambies.

Desde la app móvil verás los contratos y hitos que se te asignen, y podrás capturar la evidencia fotográfica (antes/después) de cada uno.

@component('mail::button', ['url' => $appDownloadUrl])
Abrir / descargar la app
@endcomponent

Si no reconoces esta acción, contacta a quien te asignó.

Saludos,<br>
Equipo LimpiaTuRincón
@endcomponent