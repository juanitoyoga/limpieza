<?php

namespace App\Services;

use App\Models\Contacto;
use App\Models\Contratista;
use App\Models\User;
use DomainException;

class ContratistaCreatorService
{
    public function crearDesdeContacto(Contacto $contacto): Contratista
    {
        $user = $contacto->user;

        if (! $user) {
            throw new DomainException('El contacto no tiene usuario asociado.');
        }

        $rolesPermitidos = [null, 'User'];

        if (in_array($user->role_name, $rolesPermitidos, true)) {
            $user->update(['role_name' => 'Contratista']);
        } elseif ($user->role_name !== 'Contratista') {
            throw new DomainException(
                "El usuario ya tiene el rol '{$user->role_name}'; no puede convertirse en Contratista sin antes resolver ese conflicto de rol."
            );
        }

        return Contratista::create([
            'contacto_id'  => $contacto->id,
            'proveedor_id' => $contacto->proveedor_id,
            'user_id'      => $user->id,
            'is_active'    => true,
        ]);
    }
}
