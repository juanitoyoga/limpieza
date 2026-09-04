<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ServiceSubtypeEquipment extends Pivot
{
    protected $table = 'service_subtype_equipment';

    public $incrementing = true;

    protected $fillable = [
        'service_subtype_id',
        'equipment_id',
        'quantity',
        'required',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'required' => 'boolean',
        ];
    }
}
