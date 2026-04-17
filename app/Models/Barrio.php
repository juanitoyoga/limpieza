<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Barrios
 * 
 * Representa un barrio dentro de una parroquia y sector específico.
 * Un barrio puede tener múltiples vecinos, un dirigente y un presidente.
 * 
 * @property int $id
 * @property string $id_DMQ
 * @property string $nombre
 * @property string $sector
 * @property string $parroquia
 * @property array|null $coordenadas Coordenadas geográficas en formato [lat, lng]
 * @property bool $activo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * 
 * @property Dirigente|null $dirigente Relación uno a uno con el dirigente del barrio
 * @property Presidente|null $presidente Relación uno a uno con el presidente del barrio
 * @property Collection|Vecino[] $vecinos Colección de vecinos del barrio
 *
 * @package App\Models
 */
class Barrio extends Model
{
    use SoftDeletes;

    /**
     * Nombre de la tabla en la base de datos
     *
     * @var string
     */
    protected $table = 'barrios';

    /**
     * Atributos que deben ser casteados a tipos nativos
     *
     * @var array
     */
    protected $casts = [
        'coordenadas' => 'json',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Atributos asignables en masa
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'id_DMQ',
        'sector',
        'parroquia',
        'coordenadas',
        'activo',
    ];

    /**
     * Valores predeterminados para los atributos
     *
     * @var array
     */
    protected $attributes = [
        'activo' => true,
    ];

    /**
     * Obtiene el dirigente del barrio (relación uno a uno)
     * Un barrio tiene un solo dirigente
     *
     * @return HasMany
     */
    public function dirigente(): HasMany
    {

        return $this->hasMany(Dirigente::class, 'id_DMQ' , 'id_DMQ');

    }

    /**
     * Obtiene el presidente del barrio (relación uno a uno)
     * Un barrio tiene un solo presidente
     *
     * @return HasMany
     */
    public function presidente(): HasMany
    {
        return $this->hasMany(Presidente::class, 'id_DMQ' , 'id_DMQ');
    }

    /**
     * Obtiene todos los vecinos del barrio (relación uno a muchos)
     * Un barrio puede tener múltiples vecinos
     *
     * @return HasMany
     */
    public function vecinos(): HasMany
    {
        return $this->hasMany(Vecino::class, 'id_DMQ' , 'id_DMQ');
    }

    /**
     * Scope para obtener solo barrios activos
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por parroquia
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $parroquia
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorParroquia($query, string $parroquia)
    {
        return $query->where('parroquia', $parroquia);
    }

    /**
     * Scope para filtrar por sector
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sector
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorSector($query, string $sector)
    {
        return $query->where('sector', $sector);
    }

    /**
     * Obtiene el total de vecinos del barrio
     *
     * @return int
     */
    public function getTotalVecinosAttribute(): int
    {
        return $this->vecinos()->count();
    }

    /**
     * Verifica si el barrio tiene un dirigente asignado
     *
     * @return bool
     */
    public function tieneDirigente(): bool
    {
        return $this->dirigente()->exists();
    }

    /**
     * Verifica si el barrio tiene un presidente asignado
     *
     * @return bool
     */
    public function tienePresidente(): bool
    {
        return $this->presidente()->exists();
    }

    /**
     * Obtiene la latitud de las coordenadas
     *
     * @return float|null
     */
    public function getLatitudAttribute(): ?float
    {
        return $this->coordenadas['lat'] ?? null;
    }

    /**
     * Obtiene la longitud de las coordenadas
     *
     * @return float|null
     */
    public function getLongitudAttribute(): ?float
    {
        return $this->coordenadas['lng'] ?? null;
    }

    /**
     * Retorna el nombre completo del barrio con su ubicación
     *
     * @return string
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} - {$this->sector}, {$this->parroquia}";
    }
}