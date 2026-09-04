<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionContratoServicio extends Model
{
    protected $table = 'asignaciones_contrato_servicio';

    protected $fillable = [
        'contratista_id',
        'contrato_servicio_id',
        'asignado_por',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function contratista(): BelongsTo
    {
        return $this->belongsTo(Contratista::class);
    }

    public function contratoServicio(): BelongsTo
    {
        return $this->belongsTo(ContratoServicio::class);
    }

    public function asignadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_por');
    }

    public function scopeActivas($query)
    {
        return $query->where('is_active', true);
    }
}
