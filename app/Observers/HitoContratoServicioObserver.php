<?php

namespace App\Observers;

use App\Jobs\RegistrarHitoBlockchainJob;
use App\Models\AuditEvent;
use App\Models\HitoContratoServicio;
use Illuminate\Support\Facades\DB;

class HitoContratoServicioObserver
{
    public function created(HitoContratoServicio $hito): void
    {
        if ($hito->verificado_por) {
            $this->despachar($hito, AuditEvent::EVENT_HITO_VERIFICADO, $hito->verificado_por);
        }
    }

    public function updated(HitoContratoServicio $hito): void
    {
        if ($hito->wasChanged('verificado_por') && $hito->verificado_por) {
            $this->despachar($hito, AuditEvent::EVENT_HITO_VERIFICADO, $hito->verificado_por);
            return;
        }

        if ($hito->wasChanged('aprobado_por') && $hito->aprobado_por) {
            $this->despachar($hito, AuditEvent::EVENT_HITO_APROBADO, $hito->aprobado_por);
        }
    }

    private function despachar(HitoContratoServicio $hito, string $eventType, int $userId): void
    {
        $tipoEvento = config("blockchain.tipo_evento_map.{$eventType}");

        if ($tipoEvento === null) {
            return;
        }

        // Se difiere el cálculo del hash y la consulta de evidencias
        // al momento posterior del COMMIT para no bloquear la petición web HTTP.
        DB::afterCommit(function () use ($hito, $userId, $eventType, $tipoEvento) {

            // Recalcular hash de evidencias en segundo plano (o dentro del Job)
            $hash = $hito->calcularHashEvidencias();

            $detalleEvidencias = [
                'evidencias' => $hito->evidencias()
                    ->pluck('hash_archivo', 'uuid')
                    ->filter()
                    ->toArray(),
            ];

            RegistrarHitoBlockchainJob::dispatch(
                $hito->id,
                $userId,
                $eventType,
                $tipoEvento,
                $hash,
                $detalleEvidencias
            )->onQueue('blockchain');
        });
    }
}
