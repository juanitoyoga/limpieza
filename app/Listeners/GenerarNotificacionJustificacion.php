<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\DenunciaRequiereJustificacion;
use App\Jobs\EnviarNotificacionJob;
use App\Models\Notificacion;
use App\Services\DmqPredioService;
use App\Exceptions\PredioNoResueltoException;
use Illuminate\Support\Facades\Log;

class GenerarNotificacionJustificacion implements ShouldQueue
{
    public function __construct(
        private DmqPredioService $dmqPredioService,
    ) {}

    public function handle(DenunciaRequiereJustificacion $event): void
    {
        $denuncia = $event->denuncia;
        $barrioAtributo = $event->barrioAtributo;

        try {
            $predio = $this->dmqPredioService->resolverPredio(
                (float) $denuncia->latitud,
                (float) $denuncia->longitud,
            );
        } catch (PredioNoResueltoException $e) {
            Log::error('No se pudo resolver el predio para la denuncia #' . $denuncia->id, [
                'error' => $e->getMessage(),
            ]);
            return;
        }

        $fechaNotificacion = now();
        $fechaVencimiento = $fechaNotificacion->copy()->addHours($barrioAtributo->plazo_horas);

        $notificacion = Notificacion::create([
            'denuncia_id'                  => $denuncia->id,
            'vecino_id'                    => $denuncia->vecino_id, // solo trazabilidad, no destinatario
            'barrio_id'                    => $denuncia->barrio_id,
            'ordenanza332_id'              => $denuncia->ordenanza332_id,
            'barrio_atributo_id'           => $barrioAtributo->id,

            'numero_predio'                => $predio['numero_predio'],
            'contribuyente_nombre'         => $predio['nombre_titular'],
            'contribuyente_identificacion' => $predio['identificacion'],
            'contribuyente_email'          => $predio['correo'],
            'contribuyente_telefono'       => $predio['celular'],
            'contribuyente_direccion'      => $predio['nomenclatura'],

            'estado'             => Notificacion::ESTADO_PENDIENTE,
            'plazo_horas'        => $barrioAtributo->plazo_horas,
            'fecha_notificacion' => $fechaNotificacion,
            'fecha_vencimiento'  => $fechaVencimiento,
            'medio'              => Notificacion::MEDIO_SISTEMA,
        ]);

        EnviarNotificacionJob::dispatch($notificacion);
    }
}
