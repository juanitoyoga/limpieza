<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContenidoVersion extends Model
{
    use SoftDeletes;

    protected $table = 'contenido_versiones';

    public const ESTADO_PENDIENTE = 'Pendiente';
    public const ESTADO_APROBADA  = 'Aprobada';
    public const ESTADO_RECHAZADA = 'Rechazada';
    public const ESTADO_PUBLICADA = 'Publicada';
    public const ESTADO_ARCHIVADA = 'Archivada';

    protected $fillable = [
        'contenido_item_id',
        'numero_version',
        'valores',
        'archivos',
        'fecha_inicio_vigencia',
        'fecha_fin_vigencia',
        'auth_status',
        'propuesto_por',
        'fecha_propuesta',
        'aprobado_por',
        'fecha_aprobacion',
        'rechazado_por',
        'fecha_rechazo',
        'motivo_rechazo',
        'observaciones',
        'tx_hash',
        'tx_block',
        'blockchain_timestamp',
    ];

    protected $casts = [
        'valores'                => 'array',
        'archivos'               => 'array',
        'fecha_propuesta'        => 'datetime',
        'fecha_aprobacion'       => 'datetime',
        'fecha_rechazo'          => 'datetime',
        'fecha_inicio_vigencia'  => 'datetime',
        'fecha_fin_vigencia'     => 'datetime',
        'blockchain_timestamp'   => 'datetime',
        'tx_block'               => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (ContenidoVersion $v) {
            if (is_null($v->numero_version)) {
                $item = ContenidoItem::find($v->contenido_item_id);
                $v->numero_version = $item?->siguienteNumeroVersion() ?? 1;
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ContenidoItem::class, 'contenido_item_id');
    }

    public function proponente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'propuesto_por');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function rechazador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rechazado_por');
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    public function estadoLabel(): string
    {
        return match ($this->auth_status) {
            self::ESTADO_PENDIENTE => 'Pendiente',
            self::ESTADO_APROBADA  => 'Aprobada',
            self::ESTADO_RECHAZADA => 'Rechazada',
            self::ESTADO_PUBLICADA => 'Publicada',
            self::ESTADO_ARCHIVADA => 'Archivada',
            default                => 'Desconocido',
        };
    }

    public function estadoColor(): string
    {
        return match ($this->auth_status) {
            self::ESTADO_PENDIENTE => 'bg-yellow-500',
            self::ESTADO_APROBADA  => 'bg-green-500',
            self::ESTADO_RECHAZADA => 'bg-red-600',
            self::ESTADO_PUBLICADA => 'bg-blue-600',
            self::ESTADO_ARCHIVADA => 'bg-gray-400',
            default                => 'bg-gray-300',
        };
    }

    public function valor(string $clave): mixed
    {
        return $this->valores[$clave] ?? null;
    }

    public function archivo(string $clave): ?array
    {
        return $this->archivos[$clave] ?? null;
    }
}
