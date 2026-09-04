<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Ordenanza332Resource extends JsonResource
{
    public function toArray($request)
    {
        return [

            // Identificación
            'id'             => $this->id,
            'codigo'         => $this->codigo,
            'descripcion'    => $this->descripcion,
            'tipo'           => $this->tipo,
            'nivel_gravedad' => $this->nivel_gravedad,

            // Metadatos
            'last_sync'      => $this->lastSync ?? null,
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}

