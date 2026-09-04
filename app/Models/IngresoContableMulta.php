<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class IngresoContableMulta extends Model
{
    /** @use HasFactory<\Database\Factories\IngresoContableMultaFactory> */
    use HasFactory;

    protected $fillable = [
        'multa_id',
        'beneficiario_tipo',
        'barrio_id',
        'porcentaje',
        'monto',
        'fecha_recepcion',
        'cuenta_bancaria_destino',
        'banco_destino',
        'referencia_transferencia',
        'estado_transferencia',
        'comprobante_transferencia',
        'es_simulado',
        // Blockchain
        'tx_hash',
        'verified_on_chain',
    ];
    protected $casts = [
        'fecha_recepcion' => 'datetime',
        'monto' => 'decimal:2',
        'porcentaje' => 'decimal:2',
        'es_simulado' => 'boolean',
        'verified_on_chain' => 'boolean',

    ];

    public function multa(): BelongsTo
    {
        return $this->belongsTo(Multa::class);
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function auditEvents(): MorphMany
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    public function scopePorBeneficiario($query, string $tipo)
    {
        return $query->where('beneficiario_tipo', $tipo);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_recepcion', [$desde, $hasta]);
    }
}
