<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;



class Evidencia extends Model
{
    /** @use HasFactory<\Database\Factories\EvidenciaFactory> */
    use HasFactory;

    protected $fillable = [
        'tipo',
        'disco',
        'ruta',
        'mime',
        'tamano_bytes',
        'duracion_segundos',
        'orden',
        'hash_archivo',
    ];

    public function evidenciable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return Storage::disk($this->disco)->url($this->ruta);
    }

    /** Recalcula el hash del archivo TAL COMO ESTÁ HOY, para comparar contra hash_archivo. */
    public function recalcularHashActual(): string
    {
        return hash('sha256', Storage::disk($this->disco)->get($this->ruta));
    }

    public function integridadValida(): bool
    {
        return $this->hash_archivo === $this->recalcularHashActual();
    }

    protected static function booted(): void
    {
        static::creating(function (Evidencia $evidencia) {
            $evidencia->hash_archivo ??= $evidencia->recalcularHashActual();
        });
    }
}
