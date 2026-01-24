<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    /** @use HasFactory<\Database\Factories\DepartamentoFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'description',
        'address',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];    
}

