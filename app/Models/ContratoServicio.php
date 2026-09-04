<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContratoServicio extends Model
{
    use SoftDeletes, HasFactory;

    public const ESTADO_PENDIENTE  = 'Pendiente';
    public const ESTADO_VERIFICADA = 'Verificada';
    public const ESTADO_APROBADA   = 'Aprobada';
    public const ESTADO_RECHAZADA  = 'Rechazada';
    public const ESTADO_RESCINDIDO = 'Rescindido';
    public const ESTADO_LIQUIDADO  = 'Liquidado';

    protected $table = 'contratos_servicios';

    protected $fillable = [
        'resolucion_id',
        'oferta_id',
        'proveedor_id',
        'codigo',
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin_estimada',
        'monto_total',
        'documento_original_path',
        'documento_original_hash',
        'documento_original_mime',
        'documento_rescision_path',
        'documento_rescision_hash',
        'documento_rescision_mime',
        'documento_liquidacion_path',
        'documento_liquidacion_hash',
        'documento_liquidacion_mime',
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
        'rescindido_por',
        'fecha_rescision',
        'liquidado_por',
        'fecha_liquidacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio'            => 'date',
        'fecha_fin_estimada'      => 'date',
        'monto_total'             => 'decimal:2',
        'evento_json'             => 'array',
        'tx_block'                => 'integer',
        'blockchain_block_number' => 'integer',
        'blockchain_timestamp'    => 'datetime',
        'fecha_verificacion'      => 'datetime',
        'fecha_aprobacion'        => 'datetime',
        'fecha_rechazo'           => 'datetime',
        'fecha_rescision'         => 'datetime',
        'fecha_liquidacion'       => 'datetime',
    ];
    protected static function booted(): void
    {
        static::creating(function (ContratoServicio $contrato) {
            if (is_null($contrato->resolucion_id) && $contrato->oferta_id) {
                $contrato->resolucion_id = Oferta::find($contrato->oferta_id)?->resolucion_id;
            }
        });

        static::updating(function (ContratoServicio $contrato) {
            if ($contrato->isDirty('resolucion_id')) {
                throw new \Exception('resolucion_id es inmutable y no puede modificarse después de creado el contrato.');
            }
        });
    }

    public function puedeLiquidarse(): bool
    {
        if ($this->auth_status !== self::ESTADO_APROBADA) {
            return false;
        }

        return ! $this->ordenesPago()
            ->whereIn('estado', [\App\Models\OrdenPago::ESTADO_PENDIENTE, \App\Models\OrdenPago::ESTADO_AUTORIZADA])
            ->exists();
    }

    public function ordenesPago(): HasMany
    {
        return $this->hasMany(\App\Models\OrdenPago::class, 'contrato_servicio_id');
    }


    public function formaPago(): HasMany
    {
        return $this->hasMany(ContratoFormaPago::class)->orderBy('orden');
    }

    public function resolucion(): BelongsTo
    {
        return $this->belongsTo(Resolucion::class);
    }
    public function estadoLabel(): string
    {
        return match ($this->auth_status) {
            self::ESTADO_PENDIENTE  => 'Pendiente',
            self::ESTADO_VERIFICADA => 'Verificada',
            self::ESTADO_APROBADA   => 'Aprobada',
            self::ESTADO_RECHAZADA  => 'Rechazada',
            self::ESTADO_RESCINDIDO => 'Rescindido',
            self::ESTADO_LIQUIDADO  => 'Liquidado',
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
            self::ESTADO_RESCINDIDO => 'bg-orange-600',
            self::ESTADO_LIQUIDADO  => 'bg-blue-600',
            default                 => 'bg-gray-400',
        };
    }

    /**
     * Igual que en Resolucion: mientras está Pendiente se pueden editar
     * los servicios/detalles del contrato; una vez Verificado o más
     * adelante en el flujo, quedan bloqueados.
     */
    public function puedeEditarServicios(): bool
    {
        return $this->auth_status === self::ESTADO_PENDIENTE;
    }

    /**
     * Rescindir o Liquidar solo tiene sentido desde Aprobada — es el
     * contrato ya vigente el que se puede terminar anticipadamente
     * (rescindir) o cerrar normalmente al concluir (liquidar).
     */
    public function puedeRescindirseOLiquidarse(): bool
    {
        return $this->auth_status === self::ESTADO_APROBADA;
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class, 'oferta_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
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

    public function rescindidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rescindido_por');
    }

    public function liquidador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'liquidado_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(ContratoServicioDetalle::class)->orderBy('catalogo_servicio_id');
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    public function recalcularMontoTotal(): void
    {
        $this->monto_total = $this->detalles()->sum('subtotal');
        $this->saveQuietly();
    }
}
