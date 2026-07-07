<?php

namespace App\Models;

use App\Enums\NominationEstado;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nomination extends Model
{
    use HasFactory;

    // Estados definidos en la migración
    public const ESTADO_PROPUESTA  = 'propuesta';
    public const ESTADO_VERIFICADA = 'verificada';
    public const ESTADO_RECHAZADA  = 'rechazada';
    public const ESTADO_EXPIRADA   = 'expirada';
    public const ESTADO_ANULADA   = 'anulada';
    public const ESTADO_APROBADA = 'aprobada';

    protected $table = 'nominations';

    protected $fillable = [
        'nominator_id',
        'candidate_user_id',
        'role_name',
        'issuer_type',
        'released_by',
        'document_path',
        'fecha_emision',
        'fecha_inicio_vigencia',
        'fecha_fin_vigencia',
        'estado',
        'observaciones',
        'approved_by',
        'verified_by',
        'rejected_by',
        'verified_at',
        'approved_at',
        'rejected_at',
        'hash_reference',
        'version',
        'numero_tramite',
        'is_active',
    ];

    protected $casts = [
        'fecha_emision' => 'date',

        'fecha_inicio_vigencia' => 'date',

        'fecha_fin_vigencia' => 'date',

        'verified_at' => 'datetime',

        'created_at' => 'datetime',

        'updated_at' => 'datetime',

        'approved_at' => 'datetime',

        'rejected_at' => 'datetime',

        'is_active' => 'boolean',


    ];
    // Dentro de App\Models\Nomination

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Nomination $nomination) {
            // Si ya viene definido, no lo sobreescribas
            if (!empty($nomination->numero_tramite)) {
                return;
            }

            $nomination->numero_tramite = self::generateNumeroTramite(
                issuerType: $nomination->issuer_type,
            );
        });
    }
    public function nominationDirectory(string $issuerType): string
    {
        $year = now()->year;

        $origin = match ($issuerType) {
            'DMQ' => 'DMQ',
            'JUNTA_PARROQUIAL' => 'JUNTA_PARROQUIAL',
            default => 'GENERAL',
        };

        // Usar el nombre del rol guardado en la nominación
        $role = strtoupper($this->role_name);

        return "{$origin}/{$role}/{$year}";
    }

    /**
     * Genera un número de trámite único con el formato:
     * {DEPARTAMENTO}-{ORIGEN}-{AÑO}-{SECUENCIAL}
     * Ejemplo: FIN-DMQ-2025-0001
     */
    public static function generateNumeroTramite(?string $issuerType = null): string
    {
        // Iniciales del departamento (puedes mover esto a config)
        $departmentInitials = 'FIN';

        // Origen según issuer_type de la migración
        // JUNTA_PARROQUIAL | DMQ
        $origin = match ($issuerType) {
            'JUNTA_PARROQUIAL' => 'JUN',
            'DMQ'              => 'DMQ',
            default            => 'GEN',
        };

        $year = now()->year;

        // Calcula el próximo secuencial del año (y opcionalmente por origen)
        // Nota: este enfoque cuenta registros existentes. En alta concurrencia,
        // se recomienda manejar colisiones con reintentos.
        $baseCount = self::query()
            ->whereYear('created_at', $year)
            ->when($issuerType, fn($q) => $q->where('issuer_type', $issuerType))
            ->count();

        // Intenta hasta encontrar un número único
        $attempt = 1;
        do {
            $secuencial = str_pad($baseCount + $attempt, 4, '0', STR_PAD_LEFT);
            $numero = "{$departmentInitials}-{$origin}-{$year}-{$secuencial}";
            $exists = self::query()->where('numero_tramite', $numero)->exists();
            $attempt++;
        } while ($exists);

        return $numero;
    }

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            self::ESTADO_PROPUESTA  => 'Propuesta',
            self::ESTADO_VERIFICADA => 'Verificada',
            self::ESTADO_APROBADA   => 'Aprobada',
            self::ESTADO_RECHAZADA  => 'Rechazada',
            self::ESTADO_EXPIRADA   => 'Expirada',
            self::ESTADO_ANULADA    => 'Anulada',
            default                 => 'Desconocido',
        };
    }

    public function estadoColor(): string
    {
        return match ($this->estado) {
            self::ESTADO_PROPUESTA  => 'bg-gray-500',
            self::ESTADO_VERIFICADA => 'bg-green-500',
            self::ESTADO_APROBADA   => 'bg-blue-500',
            self::ESTADO_RECHAZADA  => 'bg-red-600',
            self::ESTADO_EXPIRADA   => 'bg-yellow-600',
            self::ESTADO_ANULADA    => 'bg-red-400',
            default                 => 'bg-gray-400',
        };
    }


    public function verifyAuditIntegrity(): bool
    {
        $events = $this->auditEvents()->orderBy('event_at')->get();
        $previousHash = null;

        foreach ($events as $event) {
            $payload = [
                'nomination_id' => $event->nomination_id,
                'user_id' => $event->user_id,
                'event_type' => $event->event_type,
                'details' => $event->details,
                'previous_hash' => $previousHash,
                'timestamp' => $event->event_at->toISOString(),
            ];

            if (hash('sha256', json_encode($payload)) !== $event->event_hash) {
                return false;
            }

            $previousHash = $event->event_hash;
        }

        return true;
    }


    // Relaciones
    public function nominator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nominator_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    // Scopes
    public function scopePropuesta($query)
    {
        return $query->where('estado', self::ESTADO_PROPUESTA);
    }

    public function scopeVerificada($query)
    {
        return $query->where('estado', self::ESTADO_VERIFICADA);
    }

    public function scopeAprobada($query)
    {
        return $query->where('estado', self::ESTADO_APROBADA);
    }

    public function scopeRechazada($query)
    {
        return $query->where('estado', self::ESTADO_RECHAZADA);
    }

    public function scopeExpirada($query)
    {
        return $query->where('estado', self::ESTADO_EXPIRADA);
    }

    // Métodos auxiliares
    public function isPropuesta(): bool
    {
        return $this->estado === self::ESTADO_PROPUESTA;
    }

    public function isVerificada(): bool
    {
        return $this->estado === self::ESTADO_VERIFICADA;
    }

    public function isAprobada(): bool
    {
        return $this->estado === self::ESTADO_APROBADA;
    }

    public function isRechazada(): bool
    {
        return $this->estado === self::ESTADO_RECHAZADA;
    }

    public function isExpirada(): bool
    {
        return $this->estado === self::ESTADO_EXPIRADA;
    }

    // Control de versión
    public function incrementVersion(): void
    {
        $this->increment('version');
    }
}
