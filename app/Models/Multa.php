<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Multa extends Model
{
    use HasFactory;

    protected $table = 'multas';

    protected $fillable = [

        // Relaciones
        'denuncia_id',
        'ordenanza332_id',
        'vecino_id',
        'funcionario_id',
        'barrio_id',

        // Identificación
        'codigo_unico',
        'numero_expediente',
        'numero_resolucion',

        // Base de cálculo
        'porcentaje_salario',
        'salario_base',
        'valor_multa',

        // Distribución económica
        'porcentaje_barrio',
        'valor_barrio',

        'porcentaje_municipio',
        'valor_municipio',

        'porcentaje_plataforma',
        'valor_plataforma',

        // Estado administrativo
        'estado',

        // Fechas
        'fecha_emision',
        'fecha_notificacion',
        'fecha_vencimiento',
        'fecha_ejecutoria',
        'fecha_pago',

        // Pago
        'metodo_pago',
        'referencia_pago',
        'comprobante_pago',

        // Observaciones
        'observaciones',

        // Blockchain
        'tx_hash',
        'verified_on_chain',
    ];

    protected $casts = [

        'porcentaje_salario'      => 'decimal:2',
        'salario_base'            => 'decimal:2',
        'valor_multa'             => 'decimal:2',

        'porcentaje_barrio'       => 'decimal:2',
        'valor_barrio'            => 'decimal:2',

        'porcentaje_municipio'    => 'decimal:2',
        'valor_municipio'         => 'decimal:2',

        'porcentaje_plataforma'   => 'decimal:2',
        'valor_plataforma'        => 'decimal:2',

        'fecha_emision'           => 'datetime',
        'fecha_notificacion'      => 'datetime',
        'fecha_vencimiento'       => 'datetime',
        'fecha_ejecutoria'        => 'datetime',
        'fecha_pago'              => 'datetime',

        'verified_on_chain'       => 'boolean',
    ];

    /*
     * Relaciones
     */

    public function denuncia(): BelongsTo
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function ordenanza332(): BelongsTo
    {
        return $this->belongsTo(Ordenanza332::class);
    }

    public function vecino(): BelongsTo
    {
        return $this->belongsTo(Vecino::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    /*
     * Helpers
     */

    public function estaPagada(): bool
    {
        return $this->estado === 'pagada';
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaImpugnada(): bool
    {
        return $this->estado === 'impugnada';
    }
}
