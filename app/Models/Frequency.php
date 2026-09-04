<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    use HasFactory;
    protected $table = 'frequencies';
    protected $fillable = [
        'code',
        'name',
        'frequency_type',
        'interval_value',
        'interval_unit',
        'times_per_period',
        'description',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'interval_value' => 'integer',
            'times_per_period' => 'integer',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ];
    }
}
