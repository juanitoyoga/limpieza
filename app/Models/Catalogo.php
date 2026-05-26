<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Catalogo extends Model
{
    /** @use HasFactory<\Database\Factories\CatalogoFactory> */
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     * Es vital incluir 'external_id' para la sincronización con Wikidata.
     */
    protected $fillable = [
        'external_id',
        'nombre',
        'tipo',
        'esta_activo',
    ];

    /**
     * Casts para asegurar tipos de datos correctos.
     */
    protected $casts = [
        'esta_activo' => 'boolean',
    ];

    /**
     * Relación Muchos a Muchos con Vecino.
     * Un catálogo (ej: "Fútbol") puede estar presente en muchos vecinos.
     */
    public function vecinos(): BelongsToMany
    {
        return $this->belongsToMany(Vecino::class, 'catalogo_vecino')
                    ->withTimestamps();
    }
}