<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BarrioResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            // Identificación
            'id'          => $this->id,
            'codigo_dmq'  => $this->codigo_dmq,
            'nombre'      => $this->nombre,
            'parroquia'   => $this->parroquia,

            // Coordenadas principales
            'latitud'     => $this->latitud,
            'longitud'    => $this->longitud,

            // Polígono geográfico (si existe)
            'poligono'    => $this->poligono ?? null,

            // Metadatos
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}

