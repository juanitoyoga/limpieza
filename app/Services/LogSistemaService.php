<?php

namespace App\Services;

use App\Models\LogSistema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogSistemaService
{
    /**
     * Registra un evento en logs_sistema. Nunca lanza excepción: si falla el
     * registro (ej. BD caída), cae a Log::error() de Laravel para no tumbar
     * el flujo principal que está siendo logueado.
     *
     * @param string $origen       Clase/servicio de origen, ej: static::class
     * @param string $tipoOrigen   Categoría del evento, ej: 'livewire_bloqueo_acceso'
     * @param string $nivel        LogSistema::NIVEL_INFO|WARNING|ERROR|CRITICAL
     * @param string $comentario   Título corto y legible
     * @param string|array|null $mensajeError  Detalle largo; si es array, se serializa a JSON
     */
    public static function registrar(
        string $origen,
        string $tipoOrigen,
        string $nivel,
        string $comentario,
        string|array|null $mensajeError = null,
    ): ?LogSistema {
        try {
            $mensaje = is_array($mensajeError)
                ? json_encode($mensajeError, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                : $mensajeError;

            return LogSistema::create([
                'origen'        => $origen,
                'tipo_origen'   => $tipoOrigen,
                'nivel'         => $nivel,
                'comentario'    => $comentario,
                'mensaje_error' => $mensaje,
                'user_id'       => Auth::id(),
                'ip'            => request()?->ip(),
            ]);
        } catch (Throwable $e) {
            Log::error('LogSistemaService falló al registrar: ' . $e->getMessage(), [
                'origen_original' => $origen,
                'comentario_original' => $comentario,
            ]);

            return null;
        }
    }

    /** Atajo para excepciones capturadas en catch(). */
    public static function registrarExcepcion(string $origen, string $tipoOrigen, Throwable $e, array $contexto = []): ?LogSistema
    {
        return self::registrar(
            origen: $origen,
            tipoOrigen: $tipoOrigen,
            nivel: LogSistema::NIVEL_ERROR,
            comentario: $e->getMessage(),
            mensajeError: array_merge($contexto, [
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => $e->getTraceAsString(),
            ]),
        );
    }
}
