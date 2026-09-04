<?php

namespace App\Observers;

use App\Models\ContratoServicio;
use App\Services\ContratistaAsignacionService;
use Illuminate\Support\Facades\DB;

/**
 * Al Rescindir o Liquidar un ContratoServicio, revoca todas las
 * asignaciones activas de contratistas a ESE contrato. Si algún
 * contratista se queda sin ninguna asignación activa (ni en este ni en
 * otro contrato del proveedor), su User también pasa a inactivo — ver
 * ContratistaAsignacionService::revocarTodasDelContrato().
 */
class ContratoServicioObserver
{
    public function __construct(
        private readonly ContratistaAsignacionService $asignaciones,
    ) {}

    public function updated(ContratoServicio $contrato): void
    {
        if (! $contrato->wasChanged('auth_status')) {
            return;
        }

        if (! in_array($contrato->auth_status, [
            ContratoServicio::ESTADO_RESCINDIDO,
            ContratoServicio::ESTADO_LIQUIDADO,
        ], true)) {
            return;
        }

        DB::afterCommit(function () use ($contrato) {
            $this->asignaciones->revocarTodasDelContrato($contrato);
        });
    }
}
