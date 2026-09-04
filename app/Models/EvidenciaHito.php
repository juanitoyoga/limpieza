<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Evidencia fotográfica/video (ANTES/DESPUES) de un HitoContratoServicio,
 * capturada en campo por la app Android — posiblemente offline y
 * sincronizada luego vía `uuid`/`media_uuid` (idempotencia de sync).
 *
 * No genera transacción blockchain individual: su `hash_archivo` se agrega
 * junto con el de las demás evidencias del hito para formar
 * `hitos_contrato_servicio.hash_evidencias` (una sola transacción por hito).
 */
class EvidenciaHito extends Model
{
    use HasFactory;

    protected $table = 'evidencias_hito';

    // No usa SoftDeletes: la tabla no tiene columna deleted_at.
    public $timestamps = true;

    public const TIPO_ANTES    = 'ANTES';
    public const TIPO_DESPUES  = 'DESPUES';

    public const FORMATO_FOTO  = 'FOTO';
    public const FORMATO_VIDEO = 'VIDEO';

    protected $fillable = [
        'uuid',
        'hitos_contrato_servicio_id',
        'contrato_servicio_detalle_id',
        'tipo',
        'formato',
        'descripcion',
        'ruta_archivo',
        'media_uuid',
        'hash_archivo',
        'latitud',
        'longitud',
        'user_id',
        'capturado_en_campo_at',
        'sincronizado_at',
    ];

    protected $casts = [
        'latitud'                => 'decimal:7',
        'longitud'                => 'decimal:7',
        'capturado_en_campo_at'   => 'datetime',
        'sincronizado_at'         => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $evidencia) {
            $evidencia->uuid ??= (string) Str::uuid();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function hito()
    {
        return $this->belongsTo(HitoContratoServicio::class, 'hitos_contrato_servicio_id');
    }

    public function detalle()
    {
        return $this->belongsTo(ContratoServicioDetalle::class, 'contrato_servicio_detalle_id');
    }

    public function capturadoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAntes($query)
    {
        return $query->where('tipo', self::TIPO_ANTES);
    }

    public function scopeDespues($query)
    {
        return $query->where('tipo', self::TIPO_DESPUES);
    }

    public function scopeFotos($query)
    {
        return $query->where('formato', self::FORMATO_FOTO);
    }

    public function scopeVideos($query)
    {
        return $query->where('formato', self::FORMATO_VIDEO);
    }

    public function scopePendientesDeHash($query)
    {
        return $query->whereNull('hash_archivo');
    }

    /*
    |--------------------------------------------------------------------------
    | Blockchain
    |--------------------------------------------------------------------------
    */

    /**
     * Calcula el SHA-256 del archivo físico en `ruta_archivo` y lo persiste
     * en `hash_archivo`. Se ejecuta al confirmar la subida/sincronización
     * (no dispara ninguna transacción blockchain por sí sola).
     *
     * ⚠️ Asume el disco 'public' — ajusta si `ruta_archivo` apunta a otro
     * disco de Storage (p. ej. 's3').
     */
    public function calcularHash(?string $disco = 'public'): string
    {
        $disk = Storage::disk($disco);

        if (! $disk->exists($this->ruta_archivo)) {
            throw new \RuntimeException("Archivo no encontrado en disco '{$disco}': {$this->ruta_archivo}");
        }

        // hash_file() procesa el archivo en streaming, no lo carga completo en memoria.
        // Storage::path() solo funciona con discos locales; si migras a S3 más adelante
        // necesitarás leer por streams con readStream() en su lugar.
        $hash = hash_file('sha256', $disk->path($this->ruta_archivo));

        $this->forceFill(['hash_archivo' => $hash])->save();

        return $hash;
    }
}
