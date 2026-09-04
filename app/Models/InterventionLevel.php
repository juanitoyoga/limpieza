<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'code',
        'name',
        'intervention_type',
        'description',
        'requires_specialist',
        'requires_equipment',
        'requires_authorization',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'requires_specialist' => 'boolean',
            'requires_equipment' => 'boolean',
            'requires_authorization' => 'boolean',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ];
    }
}
