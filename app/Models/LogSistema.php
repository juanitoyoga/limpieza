<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogSistema extends Model
{
    public const NIVEL_INFO     = 'info';
    public const NIVEL_WARNING  = 'warning';
    public const NIVEL_ERROR    = 'error';
    public const NIVEL_CRITICAL = 'critical';

    protected $table = 'logs_sistema';

    protected $fillable = [
        'origen',
        'tipo_origen',
        'nivel',
        'comentario',
        'mensaje_error',
        'user_id',
        'ip',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nivelColor(): string
    {
        return match ($this->nivel) {
            self::NIVEL_INFO     => 'bg-blue-500',
            self::NIVEL_WARNING  => 'bg-yellow-500',
            self::NIVEL_ERROR    => 'bg-red-500',
            self::NIVEL_CRITICAL => 'bg-red-800',
            default              => 'bg-gray-400',
        };
    }

    public function nivelIcono(): string
    {
        return match ($this->nivel) {
            self::NIVEL_INFO     => 'fa-circle-info',
            self::NIVEL_WARNING  => 'fa-triangle-exclamation',
            self::NIVEL_ERROR    => 'fa-circle-xmark',
            self::NIVEL_CRITICAL => 'fa-skull-crossbones',
            default              => 'fa-question',
        };
    }

    /**
     * Intenta decodificar mensaje_error como JSON para mostrarlo formateado
     * en el modal de detalle. Si no es JSON válido, se muestra como texto plano.
     */
    public function mensajeErrorDecodificado(): array|string|null
    {
        if (is_null($this->mensaje_error)) {
            return null;
        }

        $decoded = json_decode($this->mensaje_error, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $this->mensaje_error;
    }
}
