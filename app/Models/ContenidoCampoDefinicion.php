<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContenidoCampoDefinicion extends Model
{
    protected $table = 'contenido_campo_definiciones';

    protected $fillable = [
        'contenido_seccion_id', 'clave', 'etiqueta', 'tipo_dato', 'requerido',
        'url_externa_obligatoria', 'imagen_ancho', 'imagen_alto', 'orden', 'activo',
    ];

    protected $casts = [
        'requerido'               => 'boolean',
        'url_externa_obligatoria' => 'boolean',
        'activo'                   => 'boolean',
    ];

    public const TIPO_DATO_TEXTO         = 'texto';
    public const TIPO_DATO_TEXTO_LARGO   = 'texto_largo';
    public const TIPO_DATO_URL           = 'url';
    public const TIPO_DATO_IMAGEN        = 'imagen';
    public const TIPO_DATO_DOCUMENTO_PDF = 'documento_pdf';

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(ContenidoSeccion::class, 'contenido_seccion_id');
    }

    /** Reemplaza al antiguo ContenidoCampoDefinicion::paraTipo(string $tipo). */
    public static function paraSeccion(int $contenidoSeccionId): \Illuminate\Support\Collection
    {
        return static::where('contenido_seccion_id', $contenidoSeccionId)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }
}
