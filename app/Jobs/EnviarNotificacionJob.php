<?php

namespace App\Jobs;


use App\Models\Notificacion;
use App\Mail\NotificacionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnviarNotificacionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(public Notificacion $notificacion) {}

    public function handle(): void
    {
        Log::info('EnviarNotificacionJob: iniciando', [
            'notificacion_id' => $this->notificacion->id,
            'denuncia_id'     => $this->notificacion->denuncia_id,
            'medio'           => $this->notificacion->medio,
            'intento'         => $this->attempts(),
        ]);

        try {
            $this->enviarCorreoDePrueba();
            $this->enviarSmsDePrueba();

            $codigoEnvio = match ($this->notificacion->medio) {
                Notificacion::MEDIO_SISTEMA  => 'sistema-' . uniqid(),
                Notificacion::MEDIO_CORREO   => 'correo-' . uniqid(),
                Notificacion::MEDIO_SMS      => 'sms-' . uniqid(),
                Notificacion::MEDIO_WHATSAPP => 'whatsapp-' . uniqid(),
                default => throw new \RuntimeException("Medio de envío no soportado: {$this->notificacion->medio}"),
            };

            Log::info('EnviarNotificacionJob: envíos completados, actualizando estado', [
                'notificacion_id' => $this->notificacion->id,
                'codigo_envio'    => $codigoEnvio,
            ]);

            $this->notificacion->update([
                'estado'       => Notificacion::ESTADO_ENVIADA,
                'enviada_at'   => now(),
                'codigo_envio' => $codigoEnvio,
                'error_envio'  => null,
            ]);

            Log::info('EnviarNotificacionJob: finalizado OK', [
                'notificacion_id' => $this->notificacion->id,
            ]);
        } catch (Throwable $e) {
            Log::error('EnviarNotificacionJob: excepción durante el envío', [
                'notificacion_id' => $this->notificacion->id,
                'medio'           => $this->notificacion->medio,
                'intento'         => $this->attempts(),
                'error'           => $e->getMessage(),
                'clase_error'     => get_class($e),
            ]);

            $this->notificacion->update(['error_envio' => $e->getMessage()]);

            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $this->notificacion->update([
            'error_envio' => 'Fallo definitivo tras reintentos: ' . $exception->getMessage(),
        ]);

        Log::error('EnviarNotificacionJob: falló definitivamente tras agotar reintentos', [
            'notificacion_id' => $this->notificacion->id,
            'tries_configurados' => $this->tries,
            'error' => $exception->getMessage(),
        ]);
    }

    // ───────────────────────────────────────────────
    // ENVÍO REAL (destino temporal: desarrollador)
    // ───────────────────────────────────────────────

    private function enviarCorreoDePrueba(): void
    {
        $destino = config('services.dev_contact.email');

        if (!$destino) {
            Log::warning('EnviarNotificacionJob: DEV_NOTIFICATION_EMAIL no configurado; correo omitido.', [
                'notificacion_id' => $this->notificacion->id,
            ]);
            return;
        }

        Log::info('EnviarNotificacionJob: enviando correo', [
            'notificacion_id' => $this->notificacion->id,
            'destino'         => $destino,
        ]);

        try {
            // Mail::raw($this->construirMensaje(), function ($message) use ($destino) {
            //     $message->to($destino)
            //         ->subject("[PRUEBA] Notificación de justificación — Denuncia #{$this->notificacion->denuncia_id}");
            // });
            Mail::to($destino)->send(new NotificacionMail($this->notificacion));

            Log::info('EnviarNotificacionJob: correo enviado sin excepción', [
                'notificacion_id' => $this->notificacion->id,
                'destino'         => $destino,
            ]);
        } catch (Throwable $e) {
            Log::error('EnviarNotificacionJob: fallo al enviar correo', [
                'notificacion_id' => $this->notificacion->id,
                'destino'         => $destino,
                'error'           => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function enviarSmsDePrueba(): void
    {
        $sid    = config('services.twilio.sid');
        $token  = config('services.twilio.token');
        $from   = config('services.twilio.from');
        $destino = config('services.dev_contact.phone');

        if (!$sid || !$token || !$from || !$destino) {
            Log::warning('EnviarNotificacionJob: credenciales Twilio o DEV_NOTIFICATION_PHONE no configurados; SMS omitido.', [
                'notificacion_id' => $this->notificacion->id,
                'sid_presente'    => (bool) $sid,
                'token_presente'  => (bool) $token,
                'from_presente'   => (bool) $from,
                'destino_presente' => (bool) $destino,
            ]);
            return;
        }

        Log::info('EnviarNotificacionJob: enviando SMS', [
            'notificacion_id' => $this->notificacion->id,
            'destino'         => $destino,
            'from'            => $from,
        ]);

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To'   => $destino,
                'Body' => $this->construirMensaje(),
            ]);

        if ($response->successful()) {
            Log::info('EnviarNotificacionJob: SMS enviado', [
                'notificacion_id' => $this->notificacion->id,
                'status'          => $response->status(),
                'twilio_sid'      => $response->json('sid'),
            ]);
        } else {
            Log::error('EnviarNotificacionJob: Twilio respondió con error', [
                'notificacion_id' => $this->notificacion->id,
                'status'          => $response->status(),
                'body'            => $response->body(),
            ]);
            throw new \RuntimeException('Twilio respondió con error: HTTP ' . $response->status() . ' — ' . $response->body());
        }
    }

    /**
     * TODO: generar el mensaje completo real (plantilla con nombre del
     * contribuyente, dirección exacta del predio, ordenanza infringida,
     * fecha límite legible, etc.). Por ahora, mensaje mínimo de prueba.
     */
    private function construirMensaje(): string
    {
        return sprintf(
            "Notificación #%d — Predio %s (%s). Plazo: %d horas. [Mensaje de prueba — backend real pendiente]",
            $this->notificacion->id,
            $this->notificacion->numero_predio,
            $this->notificacion->contribuyente_nombre,
            $this->notificacion->plazo_horas,
        );
    }
}
