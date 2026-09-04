<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolucionParticipante extends Model
{
    protected $table = 'resolucion_participantes';

    protected $fillable = [
        'resolucion_id',
        'user_id',
        'nombre_firmante',
        'documento_identidad',
        'cargo',
        'orden_firma',
    ];

    protected $casts = [
        'orden_firma' => 'integer',
    ];

    public function resolucion(): BelongsTo
    {
        return $this->belongsTo(Resolucion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
