<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use App\Models\Denuncia;
use App\Models\User;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VencerNotificacionesExpiradas extends Command
{
    protected $signature = 'notificaciones:vencer';
    protected $description = 'Marca como Vencidas las notificaciones cuyo plazo expiró y reabre la denuncia asociada.';

    public function handle(): int
    {
        Log::info('Inicio del comando notificaciones:vencer');
        // TODO: confirmar cómo se identifica al administrador (ID fijo vs. por rol)
        $adminId = User::where('role_name', 'Admin')->value('id');

        if (!$adminId) {
            $message = 'No se encontró un usuario administrador para registrar la auditoría automática.';
            $this->error($message);
            Log::error($message);

            return self::FAILURE;
        }

        $candidatas = Notificacion::query()
            ->whereIn('estado', [Notificacion::ESTADO_PENDIENTE, Notificacion::ESTADO_ENVIADA])
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<', now())
            ->with('denuncia')
            ->get();
        Log::info('Notificaciones candidatas encontradas', [
            'total' => $candidatas->count(),
        ]);
        $procesadas = 0;
        $inconsistentes = 0;
        $errores = 0;

        foreach ($candidatas as $notificacion) {
            $denuncia = $notificacion->denuncia;

            // Chequeo de consistencia: una notificación activa solo tiene
            // sentido si su denuncia sigue en ESTADO_NOTIFICADA.
            if (!$denuncia || $denuncia->estado !== Denuncia::ESTADO_NOTIFICADA) {
                $inconsistentes++;
                Log::warning('Notificación vencida con denuncia en estado inconsistente', [
                    'notificacion_id' => $notificacion->id,
                    'denuncia_id'     => $denuncia?->id,
                    'denuncia_estado' => $denuncia?->estado,
                ]);
                continue;
            }

            DB::transaction(function () use ($notificacion, $denuncia, $adminId) {
                $notificacion->update(['estado' => Notificacion::ESTADO_VENCIDA]);
                $denuncia->update(['estado' => Denuncia::ESTADO_PENDIENTE]);

                $auditNotificacion = AuditEvent::logEvent(
                    $notificacion,
                    $adminId,
                    'notificacion_vencida_automatico',
                    ['denuncia_id' => $denuncia->id]
                );
                RegistrarEventoBlockchain::dispatch($auditNotificacion->id)->onQueue('blockchain');

                $auditDenuncia = AuditEvent::logEvent(
                    $denuncia,
                    $adminId,
                    'denuncia_reabierta_por_vencimiento',
                    ['notificacion_id' => $notificacion->id]
                );
                RegistrarEventoBlockchain::dispatch($auditDenuncia->id)->onQueue('blockchain');
            });

            $procesadas++;
        }
        $summary = "Notificaciones vencidas procesadas: {$procesadas}. Inconsistentes omitidas: {$inconsistentes}. Errores: {$errores}.";
        $this->info($summary);
        Log::info($summary);

        return self::SUCCESS;
    }
}
