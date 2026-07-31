<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{

    use HasFactory, SoftDeletes;

    public const ESTADO_ACTIVO   = 'activo';
    public const ESTADO_INACTIVO = 'inactivo';

    protected $table = 'proveedores';

    protected $fillable = [
        'razon_social',
        'ruc',
        'representante_legal',
        'tipo_servicio',
        'email',
        'telefono',
        'direccion',
        'cuenta_bancaria',
        'banco',
        'estado',
    ];

    public function contratosObra()
    {
        return $this->hasMany(ContratoObra::class);
    }

    public function auditEvents()
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }

    public function estaActivo(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            self::ESTADO_ACTIVO   => 'Activo',
            self::ESTADO_INACTIVO => 'Inactivo',
            default               => 'Desconocido',
        };
    }

    public function estadoColor(): string
    {
        return match ($this->estado) {
            self::ESTADO_ACTIVO   => 'bg-green-500',
            self::ESTADO_INACTIVO => 'bg-gray-500',
            default               => 'bg-gray-400',
        };
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function contactos()
    {
        return $this->morphMany(Contacto::class, 'contactable');
    }

    public function contactoPrincipal()
    {
        return $this->morphOne(Contacto::class, 'contactable')->where('es_principal', true);
    }
}
