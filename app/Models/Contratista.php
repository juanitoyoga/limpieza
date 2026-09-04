<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contratista extends Model
{
    protected $table = 'contratistas';

    protected $fillable = [
        'proveedor_id',
        'contacto_id',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    // Roles desde los que un User puede convertirse en Contratista sin
    // conflicto: sin rol asignado todavía, o el rol genérico por defecto
    // que recibe alguien que se registró en la app sin un rol específico.
    private const ROLES_ELEGIBLES_PARA_CONTRATISTA = [null, 'User'];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function contacto(): BelongsTo
    {
        return $this->belongsTo(Contacto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionContratoServicio::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * ¿Tiene asignación activa a este contrato específico? No basta con
     * ser contratista del proveedor — debe estar asignado al contrato.
     */
    public function tieneAccesoAContrato(int $contratoServicioId): bool
    {
        return $this->asignaciones()
            ->where('contrato_servicio_id', $contratoServicioId)
            ->where('is_active', true)
            ->exists();
    }

    protected static function booted(): void
    {
        static::creating(function (Contratista $contratista) {
            $contacto = Contacto::find($contratista->contacto_id);

            if (! $contacto) {
                throw new \Exception('El contacto debe existir.');
            }

            // Deriva proveedor_id del contacto en vez de confiar en lo
            // que venga del formulario, para que no puedan desalinearse.
            $contratista->proveedor_id = $contacto->proveedor_id;

            $user = User::find($contratista->user_id);
            if (! $user) {
                throw new \Exception('El usuario no existe.');
            }

            if (in_array($user->role_name, self::ROLES_ELEGIBLES_PARA_CONTRATISTA, true)) {
                $user->update(['role_name' => 'Contratista']);
            } elseif ($user->role_name !== 'Contratista') {
                throw new \Exception(
                    "El usuario ya tiene el rol '{$user->role_name}'; no puede convertirse en Contratista sin antes resolver ese conflicto de rol."
                );
            }
        });
    }
}
