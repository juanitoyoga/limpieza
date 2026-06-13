<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Denuncia extends Model
{
    use HasFactory;

    protected $fillable = [
        // Relaciones core
        'vecino_id',
        'ordenanza332_id',   // FK al catálogo Ordenanza332 (tu nombre original ✓)
        'dirigente_id',
        'funcionario_id',

        // Datos de la denuncia
        'direccion',
        'descripcion',
        'fecha_denuncia',
        'estado',
        'multa_calculada',

        // Evidencia  ← NUEVO
        'evidencia_path',    // ruta relativa en storage/app/public
        'evidencia_tipo',    // 'foto' | 'video'

        // Geolocalización
        'latitud',
        'longitud',

        // Metadatos del dispositivo
        'app_uuid',
        'device_id',
        'os_version',
        'app_version',

        // Sincronización
        'synced',
        'synced_at',

        // Blockchain
        'file_hash',
        'tx_hash',
        'blockchain_status',
        'verified_on_chain',
    ];

    protected $casts = [
        'synced'           => 'boolean',
        'verified_on_chain'=> 'boolean',
        'fecha_denuncia'   => 'datetime',
        'synced_at'        => 'datetime',
        'multa_calculada'  => 'decimal:2',
        'latitud'          => 'decimal:7',
        'longitud'         => 'decimal:7',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    public function vecino(): BelongsTo
    {
        return $this->belongsTo(Vecino::class);
    }

    public function ordenanza332(): BelongsTo
    {
        return $this->belongsTo(Ordenanza332::class);
    }

    public function dirigente(): BelongsTo
    {
        return $this->belongsTo(Dirigente::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    // ─── Helpers ──────────────────────────────────────────────

    /** URL pública de la evidencia para el panel admin */
    public function getEvidenciaUrlAttribute(): ?string
    {
        return $this->evidencia_path
            ? asset('storage/' . $this->evidencia_path)
            : null;
    }

    public function estaResuelta(): bool
    {
        return in_array($this->estado, ['resuelto', 'rechazado']);
    }
}
