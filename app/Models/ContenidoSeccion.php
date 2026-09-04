<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Representa la ESPECIFICACIÓN de una zona de la página (banner/slides,
 * noticias, auspiciantes, logos, mejor barrio...). Son pocas filas y
 * estables: solo se crea una fila nueva cuando la especificación de esa
 * zona cambia (nuevos campos, nueva dimensión de imagen), nunca por cada
 * pieza de contenido individual — eso vive en ContenidoItem.
 */
class ContenidoSeccion extends Model
{

    protected $table = 'contenido_secciones';

    protected $fillable = [
        'area',
        'version_spec',
        'activo',
        'multiplicidad',
        'max_items',
        'plataforma',
        'descripcion',
    ];

    protected $casts = [
        'activo'    => 'boolean',
        'max_items' => 'integer',
    ];

    public function camposDefinicion(): HasMany
    {
        return $this->hasMany(ContenidoCampoDefinicion::class)
            ->where('activo', true)
            ->orderBy('orden');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ContenidoItem::class);
    }

    /** La especificación actualmente activa para un área dada. */
    public static function activaPara(string $area): ?self
    {
        return static::where('area', $area)->where('activo', true)->first();
    }

    /**
     * Todas las áreas que existen hoy en el catálogo (para pestañas de
     * navegación, por ejemplo) — sin importar si ya tienen items creados.
     */
    public static function areasDisponibles(): \Illuminate\Support\Collection
    {
        return static::where('activo', true)->orderBy('area')->pluck('area');
    }
}
