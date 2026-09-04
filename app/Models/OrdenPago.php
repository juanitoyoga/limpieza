<?php

namespace App\Models;

use App\Observers\OrdenPagoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(OrdenPagoObserver::class)]
class OrdenPago extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ordenes_pago';

    public const TIPO_ANTICIPO    = 'anticipo';
    public const TIPO_HITO        = 'hito';
    public const TIPO_SALDO_FINAL = 'saldo_final';

    public const ESTADO_PENDIENTE  = 'Pendiente';
    public const ESTADO_AUTORIZADA = 'Autorizada';
    public const ESTADO_PAGADA     = 'Pagada';
    public const ESTADO_ANULADA    = 'Anulada';

    protected $fillable = [
        'contrato_servicio_id',
        'contrato_forma_pago_id',
        'tipo',
        'monto',
        'estado',
        'registrado_por',
        'fecha_registro',
        'autorizado_por',
        'fecha_autorizacion',
        'pagado_por',
        'fecha_pago',
        'referencia_pago',
        'anulado_por',
        'fecha_anulacion',
        'motivo_anulacion',
        'observaciones',
        'hash_orden',
        'blockchain_registrado_at',
        'documento_path',
        'documento-hash',
        'documento_mime',
        'fecha_emision',
        'emitido+por',
    ];

    protected $casts = [
        'monto'                    => 'decimal:2',
        'fecha_emision'             => 'datetime',
        'emitido_por'               => 'integer',
        'fecha_registro'           => 'datetime',
        'fecha_autorizacion'       => 'datetime',
        'fecha_pago'               => 'datetime',
        'fecha_anulacion'          => 'datetime',
        'blockchain_registrado_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emitido_por');
    }

    public function contratoServicio(): BelongsTo
    {
        return $this->belongsTo(ContratoServicio::class, 'contrato_servicio_id');
    }

    public function hitos(): BelongsToMany
    {
        return $this->belongsToMany(
            HitoContratoServicio::class,
            'orden_pago_hito',
            'orden_pago_id',
            'hitos_contrato_servicio_id'
        );
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function autorizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function pagador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pagado_por');
    }

    public function anulador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    /*
    |--------------------------------------------------------------------------
    | Estado / labels
    |--------------------------------------------------------------------------
    */

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE  => 'Pendiente',
            self::ESTADO_AUTORIZADA => 'Autorizada',
            self::ESTADO_PAGADA     => 'Pagada',
            self::ESTADO_ANULADA    => 'Anulada',
            default                 => 'Desconocido',
        };
    }

    public function estadoColor(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE  => 'bg-gray-500',
            self::ESTADO_AUTORIZADA => 'bg-yellow-500',
            self::ESTADO_PAGADA     => 'bg-green-600',
            self::ESTADO_ANULADA    => 'bg-red-600',
            default                 => 'bg-gray-400',
        };
    }

    public function puedeAutorizarse(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function puedeMarcarsePagada(): bool
    {
        return $this->estado === self::ESTADO_AUTORIZADA;
    }

    public function puedeAnularse(): bool
    {
        return in_array($this->estado, [self::ESTADO_PENDIENTE, self::ESTADO_AUTORIZADA], true);
    }

    /*
    |--------------------------------------------------------------------------
    | Validaciones de negocio (reutilizables desde el componente Livewire)
    |--------------------------------------------------------------------------
    */

    /**
     * Hitos aprobados del contrato que aún no están en ninguna orden
     * Pendiente o Autorizada — disponibles para incluir en una nueva orden.
     */
    public static function hitosDisponiblesParaOrden(int $contratoServicioId): \Illuminate\Support\Collection
    {
        return HitoContratoServicio::query()
            ->whereHas('detalle.contratoServicio', fn($q) => $q->where('id', $contratoServicioId))
            ->whereNotNull('aprobado_por') // aprobado
            ->whereDoesntHave(
                'ordenesPago',
                fn($q) =>
                $q->whereIn('estado', [self::ESTADO_PENDIENTE, self::ESTADO_AUTORIZADA])
            )
            ->get();
    }

    public static function yaExisteAnticipo(int $contratoServicioId): bool
    {
        return static::where('contrato_servicio_id', $contratoServicioId)
            ->where('tipo', self::TIPO_ANTICIPO)
            ->whereNotIn('estado', [self::ESTADO_ANULADA])
            ->exists();
    }

    /**
     * Suma de lo ya autorizado/pagado — para validar que el monto de una
     * nueva orden no exceda el saldo restante del contrato.
     */
    public static function totalComprometido(int $contratoServicioId): float
    {
        return (float) static::where('contrato_servicio_id', $contratoServicioId)
            ->whereIn('estado', [self::ESTADO_AUTORIZADA, self::ESTADO_PAGADA])
            ->sum('monto');
    }
}
