<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarrioAtributo extends Model
{
    use HasFactory;

    protected $table = 'barrioatributo';

    protected $fillable = [
        'barrio_id',
        'ordenanza332_id',
        'plazo_horas',
        'nro_convenio',
    ];

    protected $casts = [
        'barrio_id' => 'integer',
        'ordenanza332_id' => 'integer',
        'plazo_horas' => 'integer',
        'nro_convenio' => 'string',
    ];

    public function barrio(): BelongsTo
    {
        return $this->belongsTo(Barrio::class);
    }

    public function ordenanza(): BelongsTo
    {
        return $this->belongsTo(Ordenanza332::class, 'ordenanza332_id');
    }
}
