<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolucionServicio extends Model
{
    use HasFactory;

    protected $table = 'resolucion_servicios';

    public const ESTADO_PENDIENTE = 'Pendiente';
    public const ESTADO_VERIFICADA = 'Verificada';
    public const ESTADO_APROBADA = 'Aprobada';
    public const ESTADO_RECHAZADA = 'Rechazada';

    public const PRIORIDAD_BAJA = 'baja';
    public const PRIORIDAD_MEDIA = 'media';
    public const PRIORIDAD_ALTA = 'alta';
    public const PRIORIDAD_URGENTE = 'urgente';

    protected $fillable = [
        'resolucion_id',
        'catalogo_servicio_id',
        'cantidad',
        'prioridad',
        'observaciones',
        'estado',
        'costo_unitario',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo_unitario' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $item) {
            if (empty($item->estado)) {
                $item->estado = self::ESTADO_PENDIENTE;
            }

            // Si no se especifica un costo manual, se toma el costo_referencial
            // vigente en el catálogo al momento de crear la línea (snapshot).
            if (is_null($item->costo_unitario) && $item->catalogo_servicio_id) {
                $item->costo_unitario = CatalogoServicios::find($item->catalogo_servicio_id)
                    ?->costo_referencial;
            }
        });
    }

    // --- Relaciones ---

    public function resolucion(): BelongsTo
    {
        return $this->belongsTo(Resolucion::class);
    }

    public function catalogoServicio(): BelongsTo
    {
        return $this->belongsTo(CatalogoServicios::class, 'catalogo_servicio_id');
    }

    // --- Accessors ---

    public function getSubtotalAttribute(): ?float
    {
        if (is_null($this->costo_unitario) || is_null($this->cantidad)) {
            return null;
        }

        return round((float) $this->costo_unitario * $this->cantidad, 2);
    }

    // --- Scopes ---

    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeDeResolucion($query, int $resolucionId)
    {
        return $query->where('resolucion_id', $resolucionId);
    }
}
