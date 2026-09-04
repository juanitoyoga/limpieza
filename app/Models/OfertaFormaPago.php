<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class OfertaFormaPago extends Model
{
    protected $table = 'oferta_forma_pago';

    public const TIPO_ANTICIPO        = 'anticipo';
    public const TIPO_CONTRA_SERVICIO = 'contra_servicio';
    public const TIPO_SALDO_FINAL     = 'saldo_final';

    protected $fillable = [
        'oferta_id',
        'orden',
        'tipo',
        'catalogo_servicio_id',
        'tipo_valor',
        'valor',
        'descripcion',
    ];

    protected $casts = [
        'orden' => 'integer',
        'valor' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $linea) {
            if ($linea->tipo === self::TIPO_CONTRA_SERVICIO && is_null($linea->catalogo_servicio_id)) {
                throw new \DomainException('Una línea "contra_servicio" requiere catalogo_servicio_id.');
            }
            if ($linea->tipo !== self::TIPO_CONTRA_SERVICIO && ! is_null($linea->catalogo_servicio_id)) {
                throw new \DomainException('catalogo_servicio_id solo aplica a líneas "contra_servicio".');
            }
            if ($linea->tipo_valor === 'porcentaje' && ($linea->valor < 0 || $linea->valor > 100)) {
                throw new \DomainException('El porcentaje debe estar entre 0 y 100.');
            }
        });
    }

    public function montoEsperado(Oferta $oferta): float
    {
        $base = $this->tipo === self::TIPO_CONTRA_SERVICIO
            ? $oferta->ofertaServicios()->where('catalogo_servicio_id', $this->catalogo_servicio_id)->sum('subtotal')
            : (float) $oferta->monto_total;

        return $this->tipo_valor === 'porcentaje'
            ? round($base * ((float) $this->valor / 100), 2)
            : (float) $this->valor;
    }

    public function formaPago(): HasMany
    {
        return $this->hasMany(OfertaFormaPago::class)->orderBy('orden');
    }

    public function oferta(): BelongsTo
    {
        return $this->belongsTo(Oferta::class);
    }

    public function catalogoServicio(): BelongsTo
    {
        return $this->belongsTo(CatalogoServicios::class);
    }
}
