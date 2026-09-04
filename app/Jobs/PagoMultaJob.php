<?php

namespace App\Jobs;

use App\Events\MultaPagada;
use App\Models\Multa;
use App\Models\AuditEvent;
use App\Models\User;
use App\Services\PagoMultaService;
use App\Services\DistribucionContableService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Class PagoMultaJob
 *
 * Job encargado de procesar de manera asíncrona y por Lotes (chunks):
 * 1. La transición de multas pendientes a pagadas (cuando han cumplido la condición/fecha).
 * 2. La distribución contable de ingresos de las multas pagadas.
 */
class PagoMultaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número máximo de reintentos para el Job completo.
     * @var int
     */
    public $tries = 1;

    /**
     * Tiempo límite de ejecución del Job en segundos (15 minutos).
     * @var int
     */
    public $timeout = 900;

    /**
     * Ejecuta el proceso principal de marcado y distribución contable de multas.
     *
     * @param PagoMultaService $pagoService Servicio para generación de datos de pago simulados/reales.
     * @param DistribucionContableService $distribucionService Servicio para la dispersión o asiento contable.
     * @return void
     */
    public function handle(
        PagoMultaService $pagoService,
        DistribucionContableService $distribucionService
    ): void {
        Log::info('[PagoMultaJob] Inicio del proceso batch de pagos y distribución.');

        // ─────────────────────────────────────────────────────────────
        // FASE 1: Transición 'pendiente' → 'pagada'
        // ─────────────────────────────────────────────────────────────
        $pagadas = 0;
        $fallidasFase1 = 0;

        Log::info('[PagoMultaJob] Iniciando Fase 1: Procesamiento de multas pendientes vencidas.');

        Multa::where('estado', Multa::ESTADO_PENDIENTE)
            ->where('fecha_emision', '<=', now())
            ->chunkById(100, function ($multas) use ($pagoService, &$pagadas, &$fallidasFase1) {
                foreach ($multas as $multa) {
                    try {
                        if ($this->marcarComoPagada($multa, $pagoService)) {
                            $pagadas++;
                            Log::debug('[PagoMultaJob] Multa procesada como pagada exitosamente', ['multa_id' => $multa->id]);
                        } else {
                            Log::notice('[PagoMultaJob] Multa omitida en Fase 1 (no cumple estado o bloqueada)', ['multa_id' => $multa->id]);
                        }
                    } catch (\Throwable $e) {
                        $fallidasFase1++;
                        Log::error('[PagoMultaJob] Falló marcar multa como pagada en Fase 1', [
                            'multa_id' => $multa->id,
                            'error'    => $e->getMessage(),
                            'file'     => $e->getFile(),
                            'line'     => $e->getLine(),
                        ]);
                    }
                }
            });

        Log::info('[PagoMultaJob] Fase 1 completada.', [
            'procesadas_exitosamente' => $pagadas,
            'fallidas'                => $fallidasFase1,
        ]);

        // ─────────────────────────────────────────────────────────────
        // FASE 2: Transición 'pagada' → 'distribuida'
        // Consulta independiente por estado para recuperar también multas de corridas previas.
        // ─────────────────────────────────────────────────────────────
        $distribuidas = 0;
        $fallidasFase2 = 0;

        Log::info('[PagoMultaJob] Iniciando Fase 2: Distribución contable de multas pagadas.');

        Multa::where('estado', Multa::ESTADO_PAGADA)
            ->chunkById(50, function ($multas) use ($distribucionService, &$distribuidas, &$fallidasFase2) {
                foreach ($multas as $multa) {
                    try {
                        $this->distribuirYMarcarDistribuida($multa, $distribucionService);
                        $distribuidas++;
                        Log::debug('[PagoMultaJob] Multa distribuida contablemente', ['multa_id' => $multa->id]);
                    } catch (\Throwable $e) {
                        $fallidasFase2++;
                        Log::error('[PagoMultaJob] Falló distribución contable en Fase 2', [
                            'multa_id' => $multa->id,
                            'error'    => $e->getMessage(),
                            'file'     => $e->getFile(),
                            'line'     => $e->getLine(),
                        ]);
                        // Permanece en 'pagada' para ser reintentada en la siguiente ejecución del Job
                    }
                }
            });

        Log::info('[PagoMultaJob] Fase 2 completada.', [
            'distribuidas_exitosamente' => $distribuidas,
            'fallidas'                  => $fallidasFase2,
        ]);

        Log::info('[PagoMultaJob] Finalización global del Job.', [
            'total_pagadas'      => $pagadas,
            'total_distribuidas' => $distribuidas,
            'total_errores'      => $fallidasFase1 + $fallidasFase2,
        ]);
    }

    /**
     * Aplica la transacción contable para cambiar el estado de la multa a 'PAGADA' con bloqueo pesimista.
     *
     * @param Multa $multaSinLock Instancia de la multa sin bloqueo.
     * @param PagoMultaService $pagoService Servicio de pago.
     * @return bool True si fue actualizada; False si fue ignorada por idempotencia.
     */
    private function marcarComoPagada(Multa $multaSinLock, PagoMultaService $pagoService): bool
    {
        return DB::transaction(function () use ($multaSinLock, $pagoService) {
            // Aplicar bloqueo pesimista (lockForUpdate) para evitar race conditions
            $multa = Multa::where('id', $multaSinLock->id)->lockForUpdate()->first();

            // Verificación de idempotencia
            if (!$multa || $multa->estado !== Multa::ESTADO_PENDIENTE) {
                Log::warning('[PagoMultaJob] Conflicto de idempotencia en marcarComoPagada', [
                    'multa_id'      => $multaSinLock->id,
                    'estado_actual' => $multa->estado ?? 'NO_ENCONTRADA'
                ]);
                return false;
            }

            // Generar la información asociada al pago
            $datosPago = $pagoService->generarDatosPago($multa);

            // Actualizar estado e información del pago
            $multa->update([
                'estado'     => Multa::ESTADO_PAGADA,
                'fecha_pago' => now(),
                ...$datosPago,
            ]);

            // Registrar evento de auditoría del sistema
            $auditEvent = AuditEvent::logEvent(
                auditable: $multa,
                userId: $this->getAdminUserId(),
                eventType: 'pago_simulado_registrado',
                details: [
                    'referencia_pago'  => $multa->referencia_pago,
                    'metodo_pago'      => $multa->metodo_pago,
                    'comprobante_pago' => $multa->comprobante_pago,
                ],
            );

            // Emitir evento del dominio
            event(new MultaPagada($multa));

            // Despachar tarea asíncrona a la blockchain una vez confirmada la transacción en BD
            DB::afterCommit(function () use ($auditEvent, $multa) {
                Log::info('[PagoMultaJob] Despachando registro en Blockchain post-commit', [
                    'multa_id'       => $multa->id,
                    'audit_event_id' => $auditEvent->id
                ]);
                RegistrarEventoBlockchain::dispatch($auditEvent->id);
            });

            return true;
        });
    }

    /**
     * Aplica la transacción contable para distribuir los ingresos de la multa y marcarla como 'DISTRIBUIDA'.
     *
     * @param Multa $multaSinLock Instancia de la multa sin bloqueo.
     * @param DistribucionContableService $distribucionService Servicio de distribución.
     * @return void
     */
    private function distribuirYMarcarDistribuida(Multa $multaSinLock, DistribucionContableService $distribucionService): void
    {
        DB::transaction(function () use ($multaSinLock, $distribucionService) {
            // Aplicar bloqueo pesimista (lockForUpdate)
            $multa = Multa::where('id', $multaSinLock->id)->lockForUpdate()->first();

            // Verificación de idempotencia
            if (!$multa || $multa->estado !== Multa::ESTADO_PAGADA) {
                Log::warning('[PagoMultaJob] Conflicto de idempotencia en distribuirYMarcarDistribuida', [
                    'multa_id'      => $multaSinLock->id,
                    'estado_actual' => $multa->estado ?? 'NO_ENCONTRADA'
                ]);
                return;
            }

            // Generar los ingresos contables correspondientes
            $distribucionService->generarIngresos($multa);

            // Actualizar el estado final de la multa
            $multa->update(['estado' => Multa::ESTADO_DISTRIBUIDA]);
        });
    }

    /**
     * Obtiene el ID del usuario administrador del sistema para auditoría.
     *
     * @return int
     * @throws \RuntimeException Si no se encuentra un usuario con role_name 'Admin'.
     */
    private function getAdminUserId(): int
    {
        return User::getSistemaAdminId();
    }
}
