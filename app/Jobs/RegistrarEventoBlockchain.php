<?php

namespace App\Jobs;

use App\Models\AuditEvent;
use App\Services\BlockchainService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Publica un AuditEvent en el smart contract AuditoriaEventos y,
 * si el modelo auditado (auditable) tiene los campos correspondientes
 * (Denuncia, Contrato, etc.), copia ahí el resultado para consulta rápida.
 *
 * Se ejecuta en queue para no bloquear la respuesta HTTP al usuario
 * mientras se confirma la transacción en Sepolia (puede tardar varios
 * segundos).
 */
class RegistrarEventoBlockchain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries;
    public int $backoff;

    public function __construct(
        public readonly int $auditEventId,
    ) {
        $this->tries   = (int) config('blockchain.job_tries', 3);
        $this->backoff = (int) config('blockchain.job_backoff', 60);
    }

    public function handle(BlockchainService $blockchain): void
    {
        $auditEvent = AuditEvent::find($this->auditEventId);

        if (!$auditEvent) {
            Log::warning("[RegistrarEventoBlockchain] AuditEvent #{$this->auditEventId} no existe, se omite.");
            return;
        }

        // Ya se registró antes (reintento duplicado) — no reenviar
        if (!empty($auditEvent->tx_hash)) {
            Log::info("[RegistrarEventoBlockchain] AuditEvent #{$auditEvent->id} ya tiene tx_hash, se omite.");
            return;
        }

        $tipoEvento = $blockchain->resolverTipoEvento($auditEvent->event_type);

        if ($tipoEvento === null) {
            // Este event_type no está mapeado a blockchain — auditoría
            // normal en BD nada más, no es un error.
            Log::info("[RegistrarEventoBlockchain] event_type '{$auditEvent->event_type}' no mapeado a blockchain, se omite.");
            return;
        }

        if (empty($auditEvent->event_hash)) {
            Log::error("[RegistrarEventoBlockchain] AuditEvent #{$auditEvent->id} no tiene event_hash, no se puede publicar.");
            return;
        }

        $resultado = $blockchain->registrar(
            tipoEvento: $tipoEvento,
            referenciaId: $auditEvent->auditable_id,
            dataHash: $auditEvent->event_hash,
        );
        // Agrega esto temporalmente después de $resultado = $blockchain->registrar(...)
        Log::info('[DEBUG propagarAModeloAuditado]', [
            'resultado'         => $resultado,
            'fillable_denuncia' => $auditEvent->auditable?->getFillable(),
            'tx_block_en_map'   => in_array('tx_block', $auditEvent->auditable?->getFillable() ?? []),
        ]);
        if (!$resultado) {
            // BlockchainService ya logueó el detalle del error.
            // Lanzar excepción para que el Job reintente según $tries/$backoff.
            throw new \RuntimeException(
                "No se pudo registrar AuditEvent #{$auditEvent->id} en blockchain (microservicio no respondió)."
            );
        }

        $auditEvent->update([
            'blockchain_hash' => $auditEvent->event_hash,
            'tx_hash'         => $resultado['txHash'],
            'tx_block'        => $resultado['blockNumber'] ?? null,
        ]);

        $this->propagarAModeloAuditado($auditEvent, $resultado);

        Log::info("[RegistrarEventoBlockchain] AuditEvent #{$auditEvent->id} registrado: {$resultado['txHash']}");
    }

    /**
     * Si el modelo auditado tiene sus propios campos blockchain
     * (como Denuncia: tx_hash, blockchain_status, verified_on_chain),
     * los actualiza para que las consultas no necesiten hacer join con
     * audit_events.
     */
    private function propagarAModeloAuditado(AuditEvent $auditEvent, array $resultado): void
    {
        $modelo = $auditEvent->auditable;

        if (!$modelo) {
            return;
        }

        $camposDisponibles = $modelo->getFillable();
        $actualizacion = [];

        if (in_array('tx_hash', $camposDisponibles)) {
            $actualizacion['tx_hash'] = $resultado['txHash'];
        }
        if (in_array('tx_block', $camposDisponibles)) {          // ← AGREGAR
            $actualizacion['tx_block'] = $resultado['blockNumber'] ?? null;
        }
        if (in_array('file_hash', $camposDisponibles) && empty($modelo->file_hash)) {
            $actualizacion['file_hash'] = $auditEvent->event_hash;
        }
        if (in_array('blockchain_status', $camposDisponibles)) {
            $actualizacion['blockchain_status'] = 'confirmado';
        }
        if (in_array('verified_on_chain', $camposDisponibles)) {
            $actualizacion['verified_on_chain'] = true;
        }
        Log::info('[propagar] actualizacion final', [
            'modelo_class' => get_class($modelo),
            'modelo_id'    => $modelo->id,
            'actualizacion' => $actualizacion,
            'blockNumber_raw' => $resultado['blockNumber'] ?? 'KEY_NO_EXISTE',
            'blockNumber_type' => gettype($resultado['blockNumber'] ?? null),
        ]);
        if (!empty($actualizacion)) {
            $modelo->update($actualizacion);
        }
    }

    public function failed(\Throwable $exception): void
    {
        $auditEvent = AuditEvent::find($this->auditEventId);

        Log::error("[RegistrarEventoBlockchain] Job falló definitivamente para AuditEvent #{$this->auditEventId}", [
            'error' => $exception->getMessage(),
        ]);

        if ($auditEvent) {
            $modelo = $auditEvent->auditable;
            if ($modelo && in_array('blockchain_status', $modelo->getFillable())) {
                $modelo->update(['blockchain_status' => 'fallido']);
            }
        }
    }
}
