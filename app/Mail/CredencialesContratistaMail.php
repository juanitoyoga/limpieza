<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Correo de bienvenida para un Contratista recién generado: incluye su
 * contraseña temporal e instrucciones para descargar la app Android,
 * iniciar sesión y cambiar la contraseña.
 */
class CredencialesContratistaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $passwordTemporal,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Acceso a LimpiaTuRincón — Registro de hitos de contrato')
            ->markdown('emails.credenciales', [
                'user'     => $this->user,
                'password' => $this->passwordTemporal,
                // ⚠️ Ajusta este link al real cuando publiques la app
                // (Play Store o distribución interna vía APK).
                'appDownloadUrl' => config('services.app_android.download_url', '#'),
            ]);
    }
}
