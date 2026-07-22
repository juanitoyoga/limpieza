<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    public const ESTADO_PENDIENTE       = 'Pendiente';
    public const ESTADO_ENVIADA         = 'Enviada';
    public const ESTADO_VERIFICADA      = 'Verificada';
    public const ESTADO_APROBADA        = 'Aprobada';
    public const ESTADO_VENCIDA         = 'Vencida';
    public const ESTADO_CERRADA         = 'Cerrada';
    public const ESTADO_RECHAZADA       = 'Rechazada';

    public const MEDIO_SISTEMA   = 'Sistema';
    public const MEDIO_CORREO    = 'Correo';
    public const MEDIO_SMS       = 'SMS';
    public const MEDIO_WHATSAPP  = 'WhatsApp';

    protected $fillable = [
        // Relaciones
        'denuncia_id',
        'user_id',
        'barrio_id',
        'ordenanza332_id',
        'barrio_atributo_id',

        // Datos del predio
        'numero_predio',

        // Datos del contribuyente (snapshot)
        'contribuyente_nombre',
        'contribuyente_identificacion',
        'contribuyente_email',
        'contribuyente_telefono',
        'contribuyente_direccion',

        // Estado
        'estado',
        'plazo_horas',
        'fecha_notificacion',
        'fecha_vencimiento',
        'leida_at',
        'cerrada_at',

        // Envío
        'medio',
        'enviada_at',
        'codigo_envio',
        'error_envio',

        // Observaciones
        'observacion',

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
        'fecha_notificacion' => 'datetime',
        'fecha_vencimiento'  => 'datetime',
        'leida_at'           => 'datetime',
        'cerrada_at'         => 'datetime',
        'enviada_at'         => 'datetime',
        'plazo_horas'        => 'integer',
        'synced'             => 'boolean',
        'verified_on_chain'  => 'boolean',
        'fecha_denuncia'     => 'datetime',
        'synced_at'          => 'datetime',
        'verificado_at'      => 'datetime',
        'aprobado_at'        => 'datetime',
        'rechazado_at'       => 'datetime',
        'latitud'            => 'decimal:7',
        'longitud'           => 'decimal:7',
        'tx_block'           => 'integer',

    ];

    public function denuncia(): BelongsTo
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function ordenanza332(): BelongsTo
    {
        return $this->belongsTo(Ordenanza332::class);
    }

    public function barrioAtributo(): BelongsTo
    {
        return $this->belongsTo(BarrioAtributo::class);
    }
    // ───────────────────────────────────────────────
    // RELACIONES PARA REVISIÓN SEGÚN ROL Y ESTADO
    // ───────────────────────────────────────────────


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
        return in_array($this->estado, ['Resuelta', 'Rechazada', 'Cerrada']);
    }

    // ───────────────────────────────────────────────
    // HELPERS: ESTADO
    // ───────────────────────────────────────────────

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE       => 'Pendiente',
            self::ESTADO_VERIFICADA      => 'Verificada',
            self::ESTADO_APROBADA        => 'Aprobada',
            self::ESTADO_VENCIDA         => 'Vencida',
            self::ESTADO_CERRADA         => 'Cerrada',
            self::ESTADO_ENVIADA         => 'Enviada',
            self::ESTADO_RECHAZADA       => 'Rechazada',
            default                      => 'Desconocido',
        };
    }

    public function estadoColor(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE       => 'bg-gray-500',
            self::ESTADO_VERIFICADA      => 'bg-blue-500',
            self::ESTADO_APROBADA        => 'bg-green-500',
            self::ESTADO_VENCIDA         => 'bg-yellow-600',
            self::ESTADO_CERRADA         => 'bg-slate-700',
            self::ESTADO_ENVIADA         => 'bg-green-500',
            self::ESTADO_RECHAZADA       => 'bg-red-500',
            default                      => 'bg-gray-400',
        };
    }

    // ───────────────────────────────────────────────
    // HELPERS: MEDIO
    // ───────────────────────────────────────────────

    public function medioLabel(): string
    {
        return match ($this->medio) {
            self::MEDIO_SISTEMA  => 'Sistema',
            self::MEDIO_CORREO   => 'Correo',
            self::MEDIO_SMS      => 'SMS',
            self::MEDIO_WHATSAPP => 'WhatsApp',
            default              => 'Desconocido',
        };
    }

    public function medioIcon(): string
    {
        return match ($this->medio) {
            self::MEDIO_SISTEMA  => 'notifications',
            self::MEDIO_CORREO   => 'mail',
            self::MEDIO_SMS      => 'sms',
            self::MEDIO_WHATSAPP => 'whatsapp',
            default              => 'help_outline',
        };
    }

    // ───────────────────────────────────────────────
    // ACCESSOR: REVISOR
    // ───────────────────────────────────────────────

    public function getRevisorAttribute(): array
    {
        $roles = [

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
