<?php

namespace App\Policies;

use App\Models\Contrato;
use App\Models\User;

class ContratoPolicy
{
    /**
     * Puede verificar si el contrato está pendiente
     * y el usuario no es quien lo ingresó.
     */
    public function verificar(User $user, Contrato $contrato): bool
    {
        return $contrato->estado === Contrato::ESTADO_PENDIENTE
            && $user->id !== $contrato->id_ingreso;
    }

    /**
     * Puede aprobar si está verificado
     * y el usuario no participó en ingreso ni verificación.
     */
    public function aprobar(User $user, Contrato $contrato): bool
    {
        return $contrato->estado === Contrato::ESTADO_VERIFICADO
            && $user->id !== $contrato->id_ingreso
            && $user->id !== $contrato->id_verificacion;
    }

    /**
     * Puede registrar rechazo solo si el contrato no está aprobado.
     */
    public function rechazar(User $user, Contrato $contrato): bool
    {
        return $contrato->estado !== Contrato::ESTADO_APROBADO;
    }
}
