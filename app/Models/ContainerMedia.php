<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerMedia extends Model
{
    use HasFactory;

    protected $table = 'containermedia';

    protected $fillable = [
        'container_id',
        'barrio_id',
        'image_path',
        'text',
        'label',
        'footer',
        'order',
    ];

    /**
     * Relación con el contenedor padre.
     */
    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    /**
     * Relación opcional con barrio.
     */
    public function barrio()
    {
        return $this->belongsTo(Barrio::class);
    }
}
