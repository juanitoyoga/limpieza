<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogoServicios extends Model
{
    /** @use HasFactory<\Database\Factories\CatalogoServiciosFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'catalogo_servicios';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'service_type_id',
        'service_subtype_id',
        'service_scope_id',
        'frequency_id',
        'intervention_level_id',
        'unit_id',
        'costo_referencial',
        'orden',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'costo_referencial' => 'decimal:2',
        'orden' => 'integer',
    ];

    // --- Scopes ---

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', true);
    }

    public function scopeDeTipo(Builder $query, int $serviceTypeId): Builder
    {
        return $query->where('service_type_id', $serviceTypeId);
    }

    // --- Accessors ---

    public function getNombreCompletoAttribute(): string
    {
        return collect([
            $this->serviceType?->name,
            $this->serviceSubtype?->name,
            $this->serviceScope?->name,
        ])
            ->filter()
            ->implode(' - ');
    }

    // --- Autogeneración de código si no se envía uno ---

    protected static function booted(): void
    {
        static::creating(function (self $servicio) {
            if (empty($servicio->codigo)) {
                $servicio->codigo = static::generarCodigo($servicio->serviceType?->code ?? 'SERV');
            }
        });
    }

    protected static function generarCodigo(string $tipoCode): string
    {
        $prefijo = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $tipoCode), 0, 4)) ?: 'SERV';
        $siguiente = static::withTrashed()
            ->whereHas('serviceType', fn($q) => $q->where('code', $tipoCode))
            ->count() + 1;

        return sprintf('%s-%03d', $prefijo, $siguiente);
    }

    // --- Relaciones ---

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function serviceSubtype(): BelongsTo
    {
        return $this->belongsTo(ServiceSubtype::class);
    }

    public function serviceScope(): BelongsTo
    {
        return $this->belongsTo(ServiceScope::class);
    }

    public function frequency(): BelongsTo
    {
        return $this->belongsTo(Frequency::class);
    }

    public function interventionLevel(): BelongsTo
    {
        return $this->belongsTo(InterventionLevel::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
    /**
     * El equipamiento ya NO se guarda en catalogo_servicios — se deriva
     * del subtipo elegido (Equipment <-> ServiceSubtype es la relación
     * real, definida a nivel de "kit" del subtipo, no por servicio
     * individual). Este helper evita tener que navegar
     * $servicio->serviceSubtype->equipment en cada vista.
     */
    public function equipoRequerido()
    {
        return $this->serviceSubtype?->equipment ?? collect();
    }

    /**
     * Necesaria para poder comprobar, ANTES de intentar borrar, si este servicio
     * ya está referenciado en alguna resolución. Como el modelo usa SoftDeletes,
     * ->delete() nunca ejecuta un DELETE real, así que un FK restrictOnDelete()
     * jamás se dispara y no sirve como única protección.
     */
    public function resolucionServicios(): HasMany
    {
        return $this->hasMany(ResolucionServicio::class, 'catalogo_servicio_id');
    }

    // public function ofertas()
    // {
    //     return $this->hasMany(OfertaServicio::class, 'catalogo_servicio_id');
    // }
}
