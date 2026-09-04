<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class ContratoFormaPago extends Model
{
    protected $table = 'contrato_forma_pago';

    public const TIPO_ANTICIPO        = 'anticipo';
    public const TIPO_CONTRA_SERVICIO = 'contra_servicio';
    public const TIPO_SALDO_FINAL     = 'saldo_final';

    protected $fillable = [
        'contrato_servicio_id',
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

    public function montoEsperado(ContratoServicio $contrato): float
    {
        $base = $this->tipo === self::TIPO_CONTRA_SERVICIO
            ? $contrato->detalles()->where('catalogo_servicio_id', $this->catalogo_servicio_id)->sum('subtotal')
            : (float) $contrato->monto_total;

        return $this->tipo_valor === 'porcentaje'
            ? round($base * ((float) $this->valor / 100), 2)
            : (float) $this->valor;
    }

    public function formaPago(): HasMany
    {
        return $this->hasMany(ContratoFormaPago::class)->orderBy('orden');
    }

    public function contratoServicio(): BelongsTo
    {
        return $this->belongsTo(ContratoServicio::class);
    }

    public function catalogoServicio(): BelongsTo
    {
        return $this->belongsTo(CatalogoServicios::class);
    }
}
