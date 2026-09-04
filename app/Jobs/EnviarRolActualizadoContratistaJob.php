<?php

namespace App\Jobs;

use App\Mail\RolActualizadoContratistaMail;
use App\Models\User;
use App\Services\LogSistemaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envía el aviso corto cuando se reutiliza una cuenta existente para
 * habilitarla como Contratista (sin contraseña nueva — ya tiene la suya).
 */
class EnviarRolActualizadoContratistaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly int $userId,
    ) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId);

        Mail::to($user->email)->send(new RolActualizadoContratistaMail($user));
    }

    public function failed(Throwable $e): void
    {
        LogSistemaService::registrarExcepcion(
            origen: static::class,
            tipoOrigen: 'contratista_rol_actualizado_fallo',
            e: $e,
            contexto: ['user_id' => $this->userId],
        );
    }
}
