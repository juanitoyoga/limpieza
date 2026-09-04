<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean'
    ];
    public function subtypes()
    {
        return $this->hasMany(ServiceSubtype::class);
    }

    public function resoluciones(): HasMany
    {
        return $this->hasMany(Resolucion::class)->orderBy('service_type_id');
    }
    public function catalogoServicios()
    {
        return $this->hasMany(CatalogoServicios::class);
    }
}
