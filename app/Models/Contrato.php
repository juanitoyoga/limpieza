<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Contrato extends Model
{
    /** @use HasFactory<\Database\Factories\ContratoFactory> */
    use HasFactory;

    use SoftDeletes;
    public const ESTADO_PENDIENTE       = 'pendiente';
    public const ESTADO_VERIFICADO      = 'verificado';
    public const ESTADO_RECHAZADO       = 'rechazado';
    public const ESTADO_FINALIZADO      = 'finalizado';
    public const ESTADO_ANULADO         = 'anulado';
    public const ESTADO_APROBADO        = 'aprobado';
    protected $table = 'contratos';

    protected $fillable = [
        'barrio_id',
        'rol_ingreso',
        'id_ingreso',
        'rol_verificacion',
        'id_verificacion',
        'rol_aprobacion',
        'id_aprobacion',
        'rol_rechazo',
        'id_rechazo',
        'motivo_rechazo',
        'numero_contrato',
        'fecha_inicio',
        'fecha_fin',
        'monto_total',
        'porcentaje_barrio',
        'porcentaje_dmq',
        'porcentaje_ltr',
        'fecha_ingreso',
        'fecha_verificacion',
        'fecha_aprobacion',
        'fecha_pago',
        'fecha_rechazo',
        'estado',
        'contrato_path',
        'document_hash',
        'blockchain_tx',
        'blockchain_network',
        'wallet_address',
        'tx_hash',
        'blockchain_at',
    ];

    protected $casts = [
        'fecha_inicio'          => 'date',
        'fecha_fin'             => 'date',
        'fecha_ingreso'         => 'datetime',
        'fecha_verificacion'    => 'datetime',
        'fecha_aprobacion'      => 'datetime',
        'fecha_pago'            => 'datetime',
        'fecha_rechazo'         => 'datetime',
        'document_hash'         => 'string',
        'blockchain_tx'         => 'string',
        'blockchain_at'         => 'datetime',
        'wallet_address'        => 'string',
        'tx_hash'               => 'string',
    ];

    // Relaciones
    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }
    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }


    public function calcularDocumentHash(string $path): string
    {
        return hash_file('sha256', storage_path('app/' . $path));
    }

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE          => 'Pendiente',
            self::ESTADO_VERIFICADO         => 'Verificado',
            self::ESTADO_APROBADO           => 'Aprobado',
            self::ESTADO_RECHAZADO          => 'Rechazado',
            self::ESTADO_FINALIZADO         => 'Finalizado',
            default                         => 'Desconocido',
        };
    }

    public function estadoColor(): string
    {
        return match ($this->estado) {
            self::ESTADO_PENDIENTE  => 'bg-gray-500',
            self::ESTADO_VERIFICADO => 'bg-green-500',
            self::ESTADO_APROBADO   => 'bg-blue-500',
            self::ESTADO_RECHAZADO  => 'bg-red-600',
            self::ESTADO_FINALIZADO   => 'bg-yellow-600',
            default                 => 'bg-gray-400',
        };
    }
    // Helpers de flujo burocrático
    public function registrarIngreso(int $funcionarioId, string $rol): void

    {
        $this->update([
            'id_ingreso' => $funcionarioId,
            'rol_ingreso' => $rol,
            'fecha_ingreso' => now(),
            'estado' => self::ESTADO_PENDIENTE,
        ]);
    }
    public function registrarBlockchain(
        string $txHash,
        string $network,
        string $documentHash
    ): void {
        $this->update([
            'blockchain_tx'      => $txHash,
            'blockchain_network' => $network,
            'document_hash'      => $documentHash,
        ]);
    }

    public function registrarVerificacion($funcionarioId, $rol)
    {
        if ($funcionarioId == $this->id_ingreso) {
            throw new \Exception("El verificador no puede ser el mismo que ingresó el contrato.");
        }

        $this->update([
            'id_verificacion' => $funcionarioId,
            'rol_verificacion' => $rol,
            'fecha_verificacion' => now(),
            'estado' => self::ESTADO_VERIFICADO,
        ]);
    }

    public function registrarAprobacion($funcionarioId, $rol)
    {
        if (in_array($funcionarioId, [$this->id_ingreso, $this->id_verificacion])) {
            throw new \Exception("El aprobador debe ser diferente al que ingresó o verificó.");
        }

        $this->update([
            'id_aprobacion' => $funcionarioId,
            'rol_aprobacion' => $rol,
            'fecha_aprobacion' => now(),
            'estado' => self::ESTADO_APROBADO,
        ]);
    }


    public function registrarRechazo($funcionarioId, $rol)
    {
        $this->update([
            'id_rechazo' => $funcionarioId,
            'rol_rechazo' => $rol,
            'fecha_rechazo' => now(),
            'estado' => self::ESTADO_RECHAZADO,
        ]);
    }

    // Cálculo de distribución
    public function calcularDistribucion()
    {
        return [
            'barrio' => $this->monto_total * ($this->porcentaje_barrio / 100),
            'dmq'    => $this->monto_total * ($this->porcentaje_dmq / 100),
            'ltr'    => $this->monto_total * ($this->porcentaje_ltr / 100),
        ];
    }
}
