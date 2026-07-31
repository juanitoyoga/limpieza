<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Oferta extends Model
{
    public const ESTADO_PENDIENTE  = 'Pendiente';
    public const ESTADO_VERIFICADA = 'Verificada';
    public const ESTADO_APROBADA   = 'Aprobada';
    public const ESTADO_RECHAZADA  = 'Rechazada';

    protected $table = 'ofertas';

    protected $fillable = [
        'codigo',
        'proveedor_id',
        'resolucion_id',
        'titulo',
        'descripcion',
        'fecha_presentacion',

        // Documentación
        'documento_original_path',
        'documento_original_hash',
        'documento_original_mime',

        // Blockchain
        'evento_json',
        'tx_hash',
        'tx_block',
        'blockchain_contract_address',
        'blockchain_block_number',
        'blockchain_timestamp',

        // Estado y responsabilidad
        'auth_status',
        'verificado_por',
        'fecha_verificacion',
        'aprobado_por',
        'fecha_aprobacion',
        'rechazado_por',
        'fecha_rechazo',

        // Auditoría
        'observaciones',
    ];

    protected $casts = [
        'fecha_presentacion'      => 'date',
        'evento_json'             => 'array',
        'blockchain_timestamp'    => 'datetime',
        'blockchain_block_number' => 'integer',
        'fecha_verificacion'      => 'datetime',
        'fecha_aprobacion'        => 'datetime',
        'fecha_rechazo'           => 'datetime',
    ];

    /* -----------------------------
       ESTADO (label + color)
    ------------------------------*/

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

    /* -----------------------------
       RELACIONES
    ------------------------------*/

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function resolucion(): BelongsTo
    {
        return $this->belongsTo(Resolucion::class);
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

    public function ofertaServicios(): HasMany
    {
        return $this->hasMany(OfertaServicio::class)->orderBy('catalogo_servicio_id');
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }
}
