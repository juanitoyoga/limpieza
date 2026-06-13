<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Barrio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barrios';

    protected $fillable = [
        'id_DMQ',
        'nombre',
        'polygon',
        'sector',
        'parroquia',
        'coordenadas',
        'activo',
    ];

    protected $casts = [
        'id' => 'integer',
        'id_DMQ' => 'string',
        'nombre' => 'string',
        'polygon' => 'json',
        'sector' => 'string',
        'parroquia' => 'string',
        'coordenadas' => 'json',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'activo' => true,
    ];

    public function dirigente(): HasMany
    {
        return $this->hasMany(Dirigente::class, 'id_DMQ', 'id_DMQ');
    }

    public function presidentes(): HasMany
    {
        return $this->hasMany(Presidente::class, 'id_DMQ', 'id_DMQ');
    }

    public function presidenteActivo(): HasOne
    {
        return $this->hasOne(Presidente::class, 'id_DMQ', 'id_DMQ')
            ->where('activo', true);
    }

    public function vecinos(): HasMany
    {
        return $this->hasMany(Vecino::class, 'id_DMQ', 'id_DMQ');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorParroquia($query, string $parroquia)
    {
        return $query->where('parroquia', $parroquia);
    }

    public function scopePorSector($query, string $sector)
    {
        return $query->where('sector', $sector);
    }

    public function getTotalVecinosAttribute(): int
    {
        return $this->vecinos()->count();
    }

    public function tieneDirigente(): bool
    {
        return $this->dirigente()->exists();
    }

    public function tienePresidente(): bool
    {
        return $this->presidentes()->exists();
    }

    public function tienePresidenteActivo(): bool
    {
        return $this->presidenteActivo()->exists();
    }

    public function getLatitudAttribute(): ?float
    {
        return $this->coordenadas['lat'] ?? null;
    }

    public function getLongitudAttribute(): ?float
    {
        return $this->coordenadas['lng'] ?? null;
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} - {$this->sector}, {$this->parroquia}";
    }
}
