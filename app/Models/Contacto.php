<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contacto extends Model

{
    use SoftDeletes;

    /**
     * Nombre de la tabla (tiene guion, así que hay que declararla).
     */
    protected $table = 'contactos';

    /**
     * Clave primaria.
     */
    protected $primaryKey = 'id';

    /**
     * Tipo de clave primaria.
     */
    protected $keyType = 'int';

    /**
     * Indica si la clave es autoincremental.
     */
    public $incrementing = true;

    /**
     * Timestamps (created_at / updated_at).
     */
    public $timestamps = true;

    /**
     * Atributos asignables en masa.
     */
    protected $fillable = [
        'tipo_id',
        'nro_id',
        'proveedor_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cargo',
        'es_principal',
        'usa_app',
        'is_active',
    ];

    /**
     * Casts de atributos.
     */
    protected $casts = [
        'proveedor_id' => 'integer',
        'es_principal' => 'boolean',
        'usa_app'      => 'boolean',
        'is_active'    => 'boolean',
        'deleted_at'   => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    // 🆕 Ver si este contacto ya generó una cuenta de usuario — reemplaza
    // el antiguo contacto.user_id (ya no existe en este modelo); el
    // vínculo con User vive en Contratista.
    public function contratista(): HasOne
    {
        return $this->hasOne(Contratista::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
