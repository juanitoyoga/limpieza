<?php

namespace App\Observers;

use App\Models\ContratoServicioDetalle;

class ContratoServicioDetalleObserver
{
    /**
     * Cubre tanto creación como edición de un detalle: cualquier cambio
     * en cantidad/costo_unitario debe reflejarse en el monto_total del
     * contrato padre. Mismo criterio que el observer de OfertaServicio.
     */
    public function saved(ContratoServicioDetalle $detalle): void
    {
        $detalle->contratoServicio?->recalcularMontoTotal();
    }

    public function deleted(ContratoServicioDetalle $detalle): void
    {
        $detalle->contratoServicio?->recalcularMontoTotal();
    }
}
