<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerNews extends Model
{
    use HasFactory;

    // Nombre de la tabla explícito (opcional si sigue convención plural)
    protected $table = 'containernews';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'container_id',
        'barrio_id',
        'title',
        'author',
        'body',
        'references',
        'verified',
    ];

    /**
     * Relación con el contenedor padre
     */
    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    /**
     * Relación con el barrio (puede ser nulo)
     */
    public function barrio()
    {
        return $this->belongsTo(Barrio::class);
    }
}
