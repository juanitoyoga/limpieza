<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contacto extends Model
{

    protected $table = 'contactos';

    protected $fillable = [
        'contactable_type',
        'contactable_id',
        'nombre',
        'cargo',
        'telefono',
        'email',
        'es_principal',
    ];

    protected $casts = [
        'es_principal' => 'bool',
    ];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
