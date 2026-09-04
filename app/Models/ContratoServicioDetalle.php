<?php

namespace App\Models;

use App\Observers\ContratoServicioDetalleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(ContratoServicioDetalleObserver::class)]
class ContratoServicioDetalle extends Model
{
    protected $table = 'contrato_servicio_detalles';

    protected $fillable = [
        'contrato_servicio_id',
        'catalogo_servicio_id',
        'cantidad',
        'costo_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad'       => 'integer',
        'costo_unitario' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function contratoServicio(): BelongsTo
    {
        return $this->belongsTo(ContratoServicio::class, 'contrato_servicio_id');
    }

    public function catalogoServicio(): BelongsTo
    {
        return $this->belongsTo(CatalogoServicios::class);
    }

    // 1 detalle → 1 hito como máximo, y solo existe DESPUÉS de que el
    // Dirigente inicia la verificación — durante la ejecución (captura de
    // ANTES/DESPUES) esto es null.
    public function hito(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(HitoContratoServicio::class);
    }

    // Evidencia física capturada por el contratista — vínculo directo,
    // independiente de si ya existe un Hito o no.
    public function evidenciasHito(): HasMany
    {
        return $this->hasMany(EvidenciaHito::class, 'contrato_servicio_detalle_id');
    }

    public function evidenciaAntes(): ?EvidenciaHito
    {
        return $this->evidenciasHito->firstWhere('tipo', EvidenciaHito::TIPO_ANTES);
    }

    public function evidenciaDespues(): ?EvidenciaHito
    {
        return $this->evidenciasHito->firstWhere('tipo', EvidenciaHito::TIPO_DESPUES);
    }

    /**
     * true cuando ya existen ambas evidencias (ANTES y DESPUES) — el
     * contratista terminó su parte. No implica que el Hito ya exista;
     * eso pasa recién cuando el Dirigente inicia la verificación.
     */
    public function ejecucionCompleta(): bool
    {
        return $this->evidenciaAntes() !== null && $this->evidenciaDespues() !== null;
    }

    /**
     * true cuando la ejecución está completa pero el Dirigente todavía
     * no inició la verificación (no existe Hito) — son los que deben
     * aparecer en el panel de "Iniciar verificación".
     */
    public function pendienteDeIniciarVerificacion(): bool
    {
        return $this->ejecucionCompleta() && ! $this->hito()->exists();
    }

    protected static function booted(): void
    {
        static::saving(function (ContratoServicioDetalle $detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->costo_unitario;
        });
    }
}
