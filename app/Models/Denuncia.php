<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denuncia extends Model
{
    use HasFactory;

    protected $fillable = [
        'vecino_id',
        'ordenanza332_id',
        'dirigente_id',
        'funcionario_id',
        'direccion',
        'descripcion',
        'fecha_denuncia',
        'estado',
        'multa_calculada',
        'latitud',
        'longitud',
        'app_uuid',
        'device_id',
        'os_version',
        'app_version',
        'synced',
        'synced_at',
        'file_hash',
        'tx_hash',
        'blockchain_status',
        'verified_on_chain',
    ];

    protected $casts = [
        'synced' => 'boolean',
        'verified_on_chain' => 'boolean',
        'fecha_denuncia' => 'datetime',
        'synced_at' => 'datetime',
    ];

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
}
