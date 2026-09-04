<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\DB;

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
        'monto_total'             => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::updating(function (Oferta $oferta) {
            if ($oferta->isDirty('resolucion_id')) {
                throw new \Exception('resolucion_id es inmutable y no puede modificarse después de creada la oferta.');
            }
        });
    }

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

    public function formaPago(): HasMany
    {
        return $this->hasMany(OfertaFormaPago::class)->orderBy('catalogo_servicio_id');
    }


    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    /**
     * Recalcula el monto_total de la oferta a partir de la suma de subtotales
     * de sus líneas (ofertaServicios). Se llama automáticamente desde el
     * observer de OfertaServicio (saved/deleted).
     */
    public function recalcularMontoTotal(): void
    {
        $this->monto_total = $this->ofertaServicios()->sum('subtotal');
        $this->saveQuietly(); // no dispara observers de Oferta al recalcular internamente
    }



    /**
     * Al aprobar esta oferta, rechaza automáticamente las demás ofertas
     * de la misma resolución que sigan activas en el proceso
     * (Pendiente o Verificada). Cada rechazo queda auditado individualmente
     * y registrado en blockchain, igual que un rechazo manual.
     *
     * Debe llamarse dentro de una transacción DB::transaction() ya abierta
     * por el componente que aprueba (ej. Aprobar.php), para que el
     * DB::afterCommit() de aquí se dispare junto con el resto de la operación.
     */
    public function rechazarCompetidoras(int $userId): \Illuminate\Support\Collection
    {
        return static::where('resolucion_id', $this->resolucion_id)
            ->where('id', '!=', $this->id)
            ->whereIn('auth_status', [self::ESTADO_PENDIENTE, self::ESTADO_VERIFICADA])
            ->get()
            ->each(function (Oferta $competidora) use ($userId) {
                $competidora->update([
                    'auth_status'   => self::ESTADO_RECHAZADA,
                    'rechazado_por' => $userId,
                    'fecha_rechazo' => now(),
                    'observaciones' => trim(($competidora->observaciones ?? '')
                        . "\nRechazada automáticamente: se aprobó otra oferta (código {$this->codigo}) para esta resolución."),
                ]);

                $evento = AuditEvent::logEvent(
                    $competidora,
                    $userId,
                    'oferta_rechazada_automatica',
                    [
                        'codigo'             => $competidora->codigo,
                        'motivo'             => 'competidora_perdedora',
                        'oferta_ganadora_id' => $this->id,
                        'oferta_ganadora_codigo' => $this->codigo,
                    ]
                );

                DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
            });
    }
}
