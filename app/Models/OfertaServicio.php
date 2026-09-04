<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfertaServicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'oferta_id',
        'catalogo_servicio_id',
        'resolucion_servicio_id',
        'cantidad',
        'costo_unitario',
        'subtotal',
        'observaciones',
    ];

    protected $casts = [
        'cantidad'       => 'integer',
        'costo_unitario' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    /* -----------------------------------------
       SUBTOTAL + MONTO TOTAL DE LA OFERTA
    ------------------------------------------*/
    protected static function booted(): void
    {
        // dentro de booted(), antes del saving que calcula subtotal

        static::saving(function (OfertaServicio $item) {
            if ($item->resolucion_servicio_id) {
                $solicitado = ResolucionServicio::find($item->resolucion_servicio_id)?->cantidad;

                if (!is_null($solicitado) && $item->cantidad > $solicitado) {
                    throw new \DomainException(
                        "La cantidad ({$item->cantidad}) no puede superar lo solicitado en la resolución ({$solicitado})."
                    );
                }
            }

            $item->subtotal = round($item->cantidad * $item->costo_unitario, 2);
        });


        static::saved(function (OfertaServicio $item) {
            $item->oferta?->recalcularMontoTotal();
        });

        static::deleted(function (OfertaServicio $item) {
            $item->oferta?->recalcularMontoTotal();
        });
    }

    /* -----------------------------------------
       RELACIONES
    ------------------------------------------*/

    // Oferta padre
    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class)->withDefault();
    }

    // Servicio del catálogo (singular)
    public function catalogoServicio(): BelongsTo
    {
        return $this->belongsTo(CatalogoServicios::class, 'catalogo_servicio_id')
            ->withDefault();
    }

    // Servicio original de la resolución
    public function resolucionServicio(): BelongsTo
    {
        return $this->belongsTo(ResolucionServicio::class)
            ->with('catalogoServicio');
    }

    // Auditoría (igual que Resolucion y Oferta)
    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }
}
