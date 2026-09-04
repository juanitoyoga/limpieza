<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MovimientoServicio extends Model
{
    protected $table = 'movimientos_servicio';

    const TIPO_SERVICIO_TERMINADO = 'SERVICIO_TERMINADO';

    protected $fillable = [
        'contrato_servicio_detalle_id',
        'tipo',
    ];

    public function contratoServicioDetalle(): BelongsTo
    {
        return $this->belongsTo(ContratoServicioDetalle::class);
    }

    public function ordenPago(): HasOne
    {
        return $this->hasOne(OrdenPago::class);
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }
}
