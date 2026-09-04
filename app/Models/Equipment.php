<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceSubtype;
use App\Models\ServiceSubtypeEquipment;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'code',
        'name',
        'equipment_type',
        'description',
        'is_consumable',
        'requires_training',
        'requires_safety_equipment',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'is_consumable' => 'boolean',
            'requires_training' => 'boolean',
            'requires_safety_equipment' => 'boolean',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function serviceSubtypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceSubtype::class,
            'service_subtype_equipment',
            'equipment_id',
            'service_subtype_id'
        )
            ->using(ServiceSubtypeEquipment::class)
            ->withPivot([
                'quantity',
                'required',
                'notes',
            ])
            ->withTimestamps();
    }
}
