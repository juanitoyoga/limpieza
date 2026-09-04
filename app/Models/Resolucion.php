<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resolucion extends Model
{
    public const ESTADO_PENDIENTE  = 'Pendiente';
    public const ESTADO_VERIFICADA = 'Verificada';
    public const ESTADO_APROBADA   = 'Aprobada';
    public const ESTADO_RECHAZADA  = 'Rechazada';

    protected $table = 'resoluciones';

    protected $fillable = [
        'codigo',
        'barrio_id',
        'titulo',
        'descripcion',
        'service_type_id',
        'fecha_resolucion',
        'documento_original_path',
        'documento_original_hash',
        'documento_original_mime',
        'numero_firmas',
        'numero_servicios',
        'evento_json',
        'tx_hash',
        'tx_block',
        'blockchain_contract_address',
        'blockchain_block_number',
        'blockchain_timestamp',
        'auth_status',
        'verificado_por',
        'fecha_verificacion',
        'aprobado_por',
        'fecha_aprobacion',
        'rechazado_por',
        'fecha_rechazo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_resolucion'        => 'date',
        'evento_json'             => 'array',
        'blockchain_timestamp'    => 'datetime',
        'numero_firmas'           => 'integer',
        'numero_servicios'        => 'integer',
        'blockchain_block_number' => 'integer',
        'fecha_verificacion'      => 'datetime',
        'fecha_aprobacion'        => 'datetime',
        'fecha_rechazo'           => 'datetime',
    ];
    public function estadoLabel(): string
    {
        return match ($this->auth_status) {
            self::ESTADO_PENDIENTE  => 'Pendiente',
            self::ESTADO_VERIFICADA => 'Verificada',
            self::ESTADO_APROBADA   => 'Aprobada',
            self::ESTADO_RECHAZADA  => 'Rechazada',
            default                 => 'Desconocido',
        };
    }

    public function estadoColor(): string
    {
        return match ($this->auth_status) {
            self::ESTADO_PENDIENTE  => 'bg-gray-500',
            self::ESTADO_VERIFICADA => 'bg-yellow-500',
            self::ESTADO_APROBADA   => 'bg-green-600',
            self::ESTADO_RECHAZADA  => 'bg-red-600',
            default                 => 'bg-gray-400',
        };
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(ResolucionParticipante::class)->orderBy('orden_firma');
    }

    public function resolucionServicios(): HasMany
    {
        return $this->hasMany(ResolucionServicio::class)->orderBy('catalogo_servicio_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function verificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
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
}
