<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso corto cuando una cuenta ya existente (rol null o 'User') se
 * habilita como Contratista. A diferencia de ContratistaCredencialesMail,
 * NO incluye contraseña — la persona ya tiene la suya.
 */
class RolActualizadoContratistaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Tu cuenta ahora tiene acceso como Contratista')
            ->markdown('emails.contratistas.rol-actualizado', [
                'user'           => $this->user,
                'appDownloadUrl' => config('services.app_android.download_url', '#'),
            ]);
    }
}
