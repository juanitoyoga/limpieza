<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    /** @use HasFactory<\Database\Factories\EvidenceFactory> */
    use HasFactory;

    protected $table = 'evidences';

    protected $fillable = [
        'file_path',
        'latitude',
        'longitude',
        'timestamp_utc',
        'device_id',
        'evidence_hash',
        'signature',
    ];
    public function vecino()
    {
        return $this->belongsTo(Vecino::class);
    }

}
