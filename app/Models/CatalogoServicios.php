<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'tipo',
        'subtipo',
        'ambito',
        'frecuencia',
        'nivel_intervencion',
        'equipamiento',
        'unidad_medida',
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

    public function scopeDeTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    // --- Accessors ---

    public function getNombreCompletoAttribute(): string
    {
        return collect([$this->tipo, $this->subtipo, $this->ambito])
            ->filter()
            ->implode(' - ');
    }

    // --- Autogeneración de código si no se envía uno ---

    protected static function booted(): void
    {
        static::creating(function (self $servicio) {
            if (empty($servicio->codigo)) {
                $servicio->codigo = static::generarCodigo($servicio->tipo);
            }
        });
    }

    protected static function generarCodigo(string $tipo): string
    {
        $prefijo = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $tipo), 0, 4)) ?: 'SERV';
        $siguiente = static::withTrashed()->where('tipo', $tipo)->count() + 1;

        return sprintf('%s-%03d', $prefijo, $siguiente);
    }

    // --- Relaciones ---

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
