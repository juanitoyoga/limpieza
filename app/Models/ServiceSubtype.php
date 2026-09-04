<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceSubtype extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'code',
        'name',
        'description',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(
            Equipment::class,
            'service_subtype_equipment',
            'service_subtype_id',
            'equipment_id'
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
