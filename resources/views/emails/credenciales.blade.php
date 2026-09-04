@component('mail::message')
# Bienvenido a LimpiaTuRincón

Hola **{{ $user->first_name }}**,

Se te ha asignado como **Contratista** para registrar el avance de un contrato de servicio del Municipio del Distrito Metropolitano de Quito. Desde la app móvil podrás capturar la evidencia fotográfica (antes/después) de cada hito.

## Tus credenciales de acceso

- **Usuario (correo):** {{ $user->email }}
- **Contraseña temporal:** `{{ $password }}`

@component('mail::panel')
Por seguridad, deberás cambiar esta contraseña la primera vez que ingreses a la app.
@endcomponent

## Cómo empezar

1. Descarga la app **LimpiaTuRincón** desde el siguiente enlace:

@component('mail::button', ['url' => $appDownloadUrl])
Descargar la app
@endcomponent

2. Abre la app e inicia sesión con tu correo y la contraseña temporal de arriba.
3. La app te pedirá crear una nueva contraseña — elige una que solo tú conozcas.
4. Una vez dentro, verás los contratos y hitos que tienes asignados. Para cada hito podrás tomar fotos de **antes** y **después** del trabajo realizado; la app las sincroniza automáticamente cuando tengas conexión (también puedes capturarlas sin internet y se sincronizan después).

Si tienes problemas para ingresar o descargar la app, contacta a quien te asignó este contrato.

Saludos,<br>
Equipo LimpiaTuRincón
@endcomponent