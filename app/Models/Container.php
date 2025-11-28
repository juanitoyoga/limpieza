<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada
     */
    protected $table = 'containers';

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'title',
        'description',
    ];

    /**
     * Ejemplo de relación futura:
     * Un contenedor puede tener muchos elementos (items).
     */
    // public function items()
    // {
    //     return $this->hasMany(Item::class);
    // }
}
