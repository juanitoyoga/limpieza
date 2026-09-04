<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};

/**
 * Una pieza CONCRETA de contenido dentro de una zona de la página:
 * el slide en la posición 1 del carrousel, una noticia puntual, el logo
 * de un auspiciante, una fila del ranking de mejores barrios.
 *
 * Esta es la tabla que antes, por error, se confundía con
 * ContenidoSeccion — cada propuesta nueva crea (o actualiza) un
 * ContenidoItem, nunca una fila nueva en contenido_secciones.
 */
class ContenidoItem extends Model
{
    protected $fillable = [
        'contenido_seccion_id',
        'identificador',
        'orden',
        'version_publicada_id',
        'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(ContenidoSeccion::class, 'contenido_seccion_id');
    }

    public function versiones(): HasMany
    {
        return $this->hasMany(ContenidoVersion::class)->orderByDesc('numero_version');
    }

    public function versionPublicada(): BelongsTo
    {
        return $this->belongsTo(ContenidoVersion::class, 'version_publicada_id');
    }

    public function versionesPendientes(): HasMany
    {
        return $this->hasMany(ContenidoVersion::class)->where('auth_status', ContenidoVersion::ESTADO_PENDIENTE);
    }

    /** Última versión sin importar su estado — para precargar formularios. */
    public function ultimaVersion(): HasOne
    {
        return $this->hasOne(ContenidoVersion::class)->latestOfMany('numero_version');
    }

    public function siguienteNumeroVersion(): int
    {
        return ($this->versiones()->max('numero_version') ?? 0) + 1;
    }
}
