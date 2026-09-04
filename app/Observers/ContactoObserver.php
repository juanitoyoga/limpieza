<?php

namespace App\Observers;

use App\Mail\CredencialesContratistaMail;
use App\Models\Contacto;
use App\Models\Contratista;
use App\Models\User;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactoObserver
{
    /**
     * Se dispara tanto en creación como en edición: un contacto puede
     * crearse primero sin usa_app y marcarse después (ej. cuando el
     * proveedor recién designa a esa persona para trabajo de campo).
     */
    public function saved(Contacto $contacto): void
    {
        if (! $contacto->usa_app || $contacto->contratista()->exists()) {
            return;
        }

        if (! $this->tieneDatosCompletos($contacto)) {
            LogSistemaService::registrar(
                origen: 'ContactoObserver',
                tipoOrigen: 'OBSERVER',
                nivel: 'WARNING',
                comentario: "Contacto #{$contacto->id} marcado con usa_app=true pero faltan datos (tipo_id/nro_id/email/first_name) — no se generó cuenta.",
            );
            return;
        }

        DB::transaction(function () use ($contacto) {
            $passwordTemporal = Str::random(12);

            $user = User::create([
                'tipo_id' => $contacto->tipo_id,
                'nro_id' => $contacto->nro_id,
                'first_name' => $contacto->first_name,
                'last_name' => $contacto->last_name,
                'email' => $contacto->email,
                'password' => $passwordTemporal, // el mutator Attribute::password() ya hashea
                'role_name' => 'Contratista',
                'is_active' => true,
            ]);

            Contratista::create([
                'contacto_id' => $contacto->id,
                'user_id' => $user->id,
                'is_active' => true,
                // proveedor_id se auto-deriva en Contratista::booted()
            ]);

            LogSistemaService::registrar(
                origen: 'ContactoObserver',
                tipoOrigen: 'OBSERVER',
                nivel: 'INFO',
                comentario: "Usuario Contratista generado automáticamente para contacto #{$contacto->id} (email: {$contacto->email})",
            );

            Mail::to($contacto->email)->send(
                new CredencialesContratistaMail($contacto, $passwordTemporal)
            );
        });
    }

    private function tieneDatosCompletos(Contacto $contacto): bool
    {
        return ! empty($contacto->first_name)
            && ! empty($contacto->email)
            && ! empty($contacto->tipo_id)
            && ! empty($contacto->nro_id);
    }
}
