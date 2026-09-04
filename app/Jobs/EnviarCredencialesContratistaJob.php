<?php

namespace App\Jobs;

use App\Mail\CredencialesContratistaMail;
use App\Models\User;
use App\Services\LogSistemaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envía el correo de bienvenida/credenciales a un Contratista recién
 * generado. Se dispara UNA sola vez, al crear el User por primera vez
 * (no en reactivaciones — esas conservan su contraseña existente).
 *
 * ShouldBeEncrypted: la contraseña en texto plano viaja en el payload
 * mientras el job espera en la tabla `jobs`; Laravel la cifra en reposo.
 */
class EnviarCredencialesContratistaJob implements ShouldQueue, ShouldBeEncrypted
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly int $userId,
        public readonly string $passwordTemporal,
    ) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId);

        Mail::to($user->email)->send(
            new CredencialesContratistaMail($user, $this->passwordTemporal)
        );
    }

    public function failed(Throwable $e): void
    {
        LogSistemaService::registrarExcepcion(
            origen: static::class,
            tipoOrigen: 'contratista_credenciales_fallo',
            e: $e,
            contexto: ['user_id' => $this->userId],
        );
    }
}
