<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denuncia extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE  = 'pendiente';
    public const ESTADO_VERIFICADA = 'verificada';
    public const ESTADO_RECHAZADA  = 'rechazada';
    public const ESTADO_EXPIRADA   = 'expirada';
    public const ESTADO_ANULADA   = 'anulada';
    public const ESTADO_APROBADA = 'aprobada';

    protected $fillable = [
        // Relaciones core
        'vecino_id',
        'barrio_id',
        'ordenanza332_id',

        // Datos de la denuncia
        'direccion',
        'direccion_gps',
        'descripcion',
        'fecha_denuncia',
        'estado',
        'multa_calculada',

        // Evidencia
        'evidencia_path',
        'evidencia_tipo',

        // Geolocalización
        'latitud',
        'longitud',

        // Verificación
        'verificado_por_id',
        'verificado_por_rol',
        'verificado_at',

        // Aprobación
        'aprobado_por_id',
        'aprobado_por_rol',
        'aprobado_at',

        // Rechazo
        'rechazado_por_id',
        'rechazado_por_rol',
        'rechazado_at',
        'motivo_rechazo',

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
        'tx_block',
        'blockchain_status',
        'verified_on_chain',
    ];

    protected $casts = [
        'synced'             => 'boolean',
        'verified_on_chain'  => 'boolean',
        'fecha_denuncia'     => 'datetime',
        'synced_at'          => 'datetime',
        'verificado_at'      => 'datetime',
        'aprobado_at'        => 'datetime',
        'rechazado_at'       => 'datetime',
        'multa_calculada'    => 'decimal:2',
        'latitud'            => 'decimal:7',
        'longitud'           => 'decimal:7',
        'tx_block'           => 'integer',

    ];

    // ───────────────────────────────────────────────
    // RELACIONES BASE
    // ───────────────────────────────────────────────

    public function vecino(): BelongsTo
    {
        return $this->belongsTo(Vecino::class);
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function ordenanza332(): BelongsTo
    {
        return $this->belongsTo(Ordenanza332::class);
    }

    // ───────────────────────────────────────────────
    // RELACIONES PARA REVISIÓN SEGÚN ROL Y ESTADO
    // ───────────────────────────────────────────────

    // DIRIGENTE
    public function verificadoPorDirigente(): BelongsTo
    {
        return $this->belongsTo(Dirigente::class, 'verificado_por_id');
    }

    public function aprobadoPorDirigente(): BelongsTo
    {
        return $this->belongsTo(Dirigente::class, 'aprobado_por_id');
    }

    public function rechazadoPorDirigente(): BelongsTo
    {
        return $this->belongsTo(Dirigente::class, 'rechazado_por_id');
    }

    // FUNCIONARIO
    public function verificadoPorFuncionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'verificado_por_id');
    }

    public function aprobadoPorFuncionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'aprobado_por_id');
    }

    public function rechazadoPorFuncionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class, 'rechazado_por_id');
    }

    // SUPERVISOR
    public function verificadoPorSupervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class, 'verificado_por_id');
    }

    public function aprobadoPorSupervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class, 'aprobado_por_id');
    }

    public function rechazadoPorSupervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class, 'rechazado_por_id');
    }

    // ───────────────────────────────────────────────
    // HELPERS
    // ───────────────────────────────────────────────

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
    public function estadoLabel(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE  => 'Pendiente',
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
            self::ESTADO_PENDIENTE  => 'bg-gray-500',
            self::ESTADO_VERIFICADA => 'bg-green-500',
            self::ESTADO_APROBADA   => 'bg-blue-500',
            self::ESTADO_RECHAZADA  => 'bg-red-600',
            self::ESTADO_EXPIRADA   => 'bg-yellow-600',
            self::ESTADO_ANULADA    => 'bg-red-400',
            default                 => 'bg-gray-400',
        };
    }

    // ───────────────────────────────────────────────
    // ACCESSOR: REVISOR
    // ───────────────────────────────────────────────

    public function getRevisorAttribute(): array
    {
        $roles = [
            'Dirigente'   => [
                'Verificado' => 'verificadoPorDirigente',
                'Aprobado'   => 'aprobadoPorDirigente',
                'Rechazado'  => 'rechazadoPorDirigente',
            ],
            'Funcionario' => [
                'Verificado' => 'verificadoPorFuncionario',
                'Aprobado'   => 'aprobadoPorFuncionario',
                'Rechazado'  => 'rechazadoPorFuncionario',
            ],
            'Supervisor'  => [
                'Verificado' => 'verificadoPorSupervisor',
                'Aprobado'   => 'aprobadoPorSupervisor',
                'Rechazado'  => 'rechazadoPorSupervisor',
            ],
        ];

        // Determinar datos según estado
        $info = match ($this->estado) {
            'Verificado' => [
                'rol'   => $this->verificado_por_rol,
                'id'    => $this->verificado_por_id,
                'fecha' => $this->verificado_at,
            ],
            'Aprobado' => [
                'rol'   => $this->aprobado_por_rol,
                'id'    => $this->aprobado_por_id,
                'fecha' => $this->aprobado_at,
            ],
            'Rechazado' => [
                'rol'   => $this->rechazado_por_rol,
                'id'    => $this->rechazado_por_id,
                'fecha' => $this->rechazado_at,
            ],
            default => null,
        };

        if (!$info) {
            return [
                'id'     => null,
                'nombre' => null,
                'rol'    => null,
                'fecha'  => null,
            ];
        }

        // Si el rol no existe en el mapa
        if (!isset($roles[$info['rol']][$this->estado])) {
            return [
                'id'     => $info['id'],
                'nombre' => null,
                'rol'    => $info['rol'],
                'fecha'  => $info['fecha'],
            ];
        }

        // Obtener relación correcta según rol y estado
        $relation = $roles[$info['rol']][$this->estado];
        $usuario = $this->$relation?->user;

        return [
            'id'     => $info['id'],
            'nombre' => $usuario ? $usuario->first_name . ' ' . $usuario->last_name : null,
            'rol'    => $info['rol'],
            'fecha'  => $info['fecha'],
        ];
    }

    public function recordBlockchainTransaction(string $blockchainHash, string $txHash, ?int $txBlock = null): void
    {
        $this->update([
            'blockchain_hash' => $blockchainHash,
            'tx_hash'         => $txHash,
            'tx_block'        => $txBlock,
        ]);
    }
}
