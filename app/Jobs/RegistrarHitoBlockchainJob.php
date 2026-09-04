<?php

namespace App\Jobs;

use App\Models\AuditEvent;
use App\Models\HitoContratoServicio;
use App\Services\BlockchainService;
use App\Services\LogSistemaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Publica un evento de HitoContratoServicio en el smart contract
 * AuditoriaEventos vía BlockchainService, y deja constancia en AuditEvent.
 *
 * Solo se dispara para verificación y aprobación (AuditEvent::EVENT_HITO_VERIFICADO
 * / EVENT_HITO_APROBADO) — la creación del hito y la carga de cada
 * EvidenciaHito individual no generan transacción propia; sus hashes viajan
 * agregados en $dataHash y detallados en `details`.
 *
 * Cola: php artisan queue:work --queue=blockchain --tries=3 --backoff=60 --verbose
 */
class RegistrarHitoBlockchainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries;
    public int $backoff;

    public function __construct(
        public readonly int $hitoId,
        public readonly int $userId,
        public readonly string $eventType,   // AuditEvent::EVENT_HITO_VERIFICADO | EVENT_HITO_APROBADO
        public readonly int $tipoEvento,     // uint8 para el contrato Solidity
        public readonly string $dataHash,    // hash agregado de evidencias
        public readonly ?array $detalles = null,
    ) {
        $this->tries   = (int) config('blockchain.job_tries', 3);
        $this->backoff = (int) config('blockchain.job_backoff', 60);
    }

    public function handle(BlockchainService $blockchain): void
    {
        $hito = HitoContratoServicio::find($this->hitoId);

        if (! $hito) {
            // El hito ya no existe (borrado); nada que registrar.
            return;
        }

        $resultado = $blockchain->registrar($this->tipoEvento, $this->hitoId, $this->dataHash);

        if ($resultado === null) {
            // BlockchainService ya logueó el detalle del error.
            // Lanzamos para que el worker reintente según $tries/$backoff.
            throw new \RuntimeException(
                "No se pudo registrar el hito #{$this->hitoId} ({$this->eventType}) en blockchain."
            );
        }

        AuditEvent::logEvent(
            auditable: $hito,
            userId: $this->userId,
            eventType: $this->eventType,
            details: array_merge($this->detalles ?? [], [
                'tx_hash'      => $resultado['txHash'] ?? null,
                'block_number' => $resultado['blockNumber'] ?? null,
                'gas_usado'    => $resultado['gasUsado'] ?? null,
                'explorer_url' => $resultado['explorerUrl'] ?? null,
                'data_hash'    => $this->dataHash,
            ]),
        );

        $hito->forceFill(['blockchain_registrado_at' => now()])->save();
    }

    public function failed(\Throwable $e): void
    {
        LogSistemaService::registrarExcepcion(
            origen: static::class,
            tipoOrigen: 'blockchain_hito_fallo',
            e: $e,
            contexto: [
                'hito_id'    => $this->hitoId,
                'event_type' => $this->eventType,
                'tipo_evento' => $this->tipoEvento,
            ],
        );
    }
}
