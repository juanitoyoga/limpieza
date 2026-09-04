<?php

namespace App\Models;

use App\Observers\HitoContratoServicioObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Hito (milestone) de avance dentro de un ContratoServicio.
 *
 * Agrupa la evidencia fotográfica (EvidenciaHito) capturada en campo —
 * posiblemente offline y sincronizada luego vía `uuid`. El hash de TODAS
 * las evidencias del hito se agrega en `hash_evidencias` para registrar
 * una única transacción blockchain por hito (no una por foto), siguiendo
 * la misma lógica de optimización de costos usada en ContratoServicio.
 */
#[ObservedBy(HitoContratoServicioObserver::class)]
class HitoContratoServicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hitos_contrato_servicio';

    protected $fillable = [
        'uuid',
        'contratos_servicios_id',
        'contrato_servicio_detalle_id',
        'descripcion_servicio',
        'hash_evidencias',
        'blockchain_registrado_at',
        'user_id',
        'capturado_en_campo_at',
        'sincronizado_at',
        'verificado_por',
        'verificado_at',
        'aprobado_por',
        'aprobado_at',
        'rechazado_por',
        'rechazado_at',
    ];

    protected $casts = [
        'capturado_en_campo_at'    => 'datetime',
        'sincronizado_at'          => 'datetime',
        'verificado_at'            => 'datetime',
        'aprobado_at'              => 'datetime',
        'rechazado_at'             => 'datetime',
        'blockchain_registrado_at' => 'datetime',
    ];

    // No existe columna `estado`: se deriva de las columnas *_por / *_at,
    // igual que en Resolucion/Oferta.
    public const ESTADO_PENDIENTE  = 'pendiente';
    public const ESTADO_VERIFICADO = 'verificado';
    public const ESTADO_APROBADO   = 'aprobado';
    public const ESTADO_RECHAZADO  = 'rechazado';

    protected static function booted(): void
    {
        static::creating(function (self $hito) {
            $hito->uuid ??= (string) Str::uuid();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function contratoServicio()
    {
        return $this->belongsTo(ContratoServicio::class, 'contratos_servicios_id');
    }

    // ⚠️ Verifica el nombre real del modelo/tabla del detalle — asumí
    // ContratoServicioDetalle porque el índice único es
    // hitos_detalle_orden_unique(contrato_servicio_detalle_id). Ajusta si
    // el nombre de clase real es distinto.
    public function detalle()
    {
        return $this->belongsTo(ContratoServicioDetalle::class, 'contrato_servicio_detalle_id');
    }

    public function evidencias()
    {
        return $this->hasMany(EvidenciaHito::class, 'hitos_contrato_servicio_id');
    }

    // Atajos por tipo, usados típicamente al mostrar el hito en el panel
    public function evidenciasAntes()
    {
        return $this->evidencias()->where('tipo', EvidenciaHito::TIPO_ANTES);
    }

    public function evidenciasDespues()
    {
        return $this->evidencias()->where('tipo', EvidenciaHito::TIPO_DESPUES);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verificadoPor()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function rechazadoPor()
    {
        return $this->belongsTo(User::class, 'rechazado_por');
    }

    /*
    |--------------------------------------------------------------------------
    | Estado (derivado)
    |--------------------------------------------------------------------------
    */

    public function getEstadoAttribute(): string
    {
        if ($this->rechazado_por) {
            return self::ESTADO_RECHAZADO;
        }
        if ($this->aprobado_por) {
            return self::ESTADO_APROBADO;
        }
        if ($this->verificado_por) {
            return self::ESTADO_VERIFICADO;
        }
        return self::ESTADO_PENDIENTE;
    }

    public function scopePendientes($query)
    {
        return $query->whereNull('verificado_por')
            ->whereNull('aprobado_por')
            ->whereNull('rechazado_por');
    }

    public function scopeVerificados($query)
    {
        return $query->whereNotNull('verificado_por')
            ->whereNull('aprobado_por')
            ->whereNull('rechazado_por');
    }

    public function scopeAprobados($query)
    {
        return $query->whereNotNull('aprobado_por');
    }

    public function scopeRechazados($query)
    {
        return $query->whereNotNull('rechazado_por');
    }

    /**
     * Un hito solo puede verificarse si tiene al menos una evidencia
     * ANTES y una DESPUES — el par que demuestra el trabajo realizado.
     */
    public function tieneParCompleto(): bool
    {
        return $this->evidencias()->where('tipo', EvidenciaHito::TIPO_ANTES)->exists()
            && $this->evidencias()->where('tipo', EvidenciaHito::TIPO_DESPUES)->exists();
    }

    public function estaVerificado(): bool
    {
        return ! is_null($this->verificado_por);
    }

    public function estaAprobado(): bool
    {
        return ! is_null($this->aprobado_por);
    }

    /*
    |--------------------------------------------------------------------------
    | Blockchain
    |--------------------------------------------------------------------------
    */

    /**
     * Calcula y persiste el hash agregado (batch) de todas las evidencias
     * del hito. Este es el `dataHash` que se envía a
     * BlockchainService::registrar() — así se paga una sola transacción
     * por hito en vez de una por cada foto.
     *
     * Requiere que cada EvidenciaHito ya tenga su `hash_archivo` calculado
     * (ver EvidenciaHito::calcularHash()).
     */

    public function calcularHashEvidencias(): string
    {
        // Asegurar que todas las evidencias tengan su hash procesado
        foreach ($this->evidencias as $evidencia) {
            if (empty($evidencia->hash_archivo)) {
                $evidencia->calcularHash();
            }
        }

        $hashesOrdenados = $this->evidencias()
            ->orderBy('id')
            ->pluck('hash_archivo')
            ->filter()
            ->implode('');

        $hash = hash('sha256', $hashesOrdenados . $this->uuid);

        $this->forceFill(['hash_evidencias' => $hash])->save();

        return $hash;
    }

    public function ordenesPago()
    {
        return $this->belongsToMany(
            OrdenPago::class,
            'orden_pago_hito',
            'hitos_contrato_servicio_id',
            'orden_pago_id'
        );
    }

    /**
     * true si al hito le falta registrarse en blockchain (tiene hash pero
     * no fecha de registro). Solo aplica a hitos verificados/aprobados —
     * la creación y la carga de evidencia individual no publican evento.
     */
    public function getPendienteBlockchainAttribute(): bool
    {
        return filled($this->hash_evidencias) && is_null($this->blockchain_registrado_at);
    }
}
