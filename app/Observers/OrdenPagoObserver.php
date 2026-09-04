<?php

namespace App\Observers;

use App\Models\{OrdenPago, AuditEvent, LogSistema};

use App\Jobs\RegistrarEventoBlockchain;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\DB;

class OrdenPagoObserver
{
    public function updated(OrdenPago $orden): void
    {
        if ($orden->isDirty('estado') && $orden->estado === OrdenPago::ESTADO_AUTORIZADA) {
            $this->registrarEvento($orden, $orden->autorizado_por, 'orden_pago_autorizada');
        }

        if ($orden->isDirty('estado') && $orden->estado === OrdenPago::ESTADO_PAGADA) {
            $this->registrarEvento($orden, $orden->pagado_por, 'orden_pago_pagada');
        }

        if ($orden->isDirty('estado') && $orden->estado === OrdenPago::ESTADO_ANULADA) {
            $this->registrarEvento($orden, $orden->anulado_por, 'orden_pago_anulada');
        }
    }

    private function registrarEvento(OrdenPago $orden, ?int $userId, string $tipoEvento): void
    {
        try {
            $evento = AuditEvent::logEvent($orden, $userId, $tipoEvento, [
                'contrato_servicio_id' => $orden->contrato_servicio_id,
                'tipo'   => $orden->tipo,
                'monto'  => $orden->monto,
                'estado' => $orden->estado,
            ]);

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        } catch (\Throwable $e) {
            LogSistemaService::registrar(
                origen: static::class,
                tipoOrigen: 'orden_pago_observer',
                nivel: LogSistema::NIVEL_ERROR,
                comentario: "Fallo sincronizando orden de pago {$orden->id} con blockchain",
                mensajeError: $e->getMessage(),
            );
        }
    }
}
