<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DenunciaResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            // ───────────────────────────────────────────────
            // Identificación
            // ───────────────────────────────────────────────
            'id'               => $this->id,
            'estado'           => $this->estado,
            'fecha_denuncia'   => $this->fecha_denuncia?->toIso8601String(),

            // ───────────────────────────────────────────────
            // Relaciones
            // ───────────────────────────────────────────────
            'vecino'           => new VecinoResource($this->whenLoaded('vecino')),
            'ordenanza'        => new Ordenanza332Resource($this->whenLoaded('ordenanza332')),

            // 🆕 Barrio — ya se cargaba con with('barrio') pero nunca se exponía
            'barrio' => $this->whenLoaded('barrio', function () {
                return $this->barrio ? [
                    'id'     => $this->barrio->id,
                    'id_DMQ' => $this->barrio->id_DMQ,
                    'nombre' => $this->barrio->nombre,
                ] : null;
            }),

            // Datos de auditoría dinámica usando tu accessor del modelo
            'auditoria_revisor' => $this->revisor,

            // ───────────────────────────────────────────────
            // Datos de la denuncia
            // ───────────────────────────────────────────────
            'direccion'        => $this->direccion,
            'direccion_gps'    => $this->direccion_gps,
            'descripcion'      => $this->descripcion,
            'multa_calculada'  => $this->multa_calculada,

            // ───────────────────────────────────────────────
            // Evidencia
            // ───────────────────────────────────────────────
            'evidencia' => [
                'tipo' => $this->evidencia_tipo,
                'path' => $this->evidencia_path,
                'url'  => $this->evidencia_url, // accessor del modelo
            ],

            // ───────────────────────────────────────────────
            // Geolocalización
            // ───────────────────────────────────────────────
            'ubicacion' => [
                'latitud'  => $this->latitud,
                'longitud' => $this->longitud,
            ],

            // ───────────────────────────────────────────────
            // Metadatos del dispositivo
            // ───────────────────────────────────────────────
            'dispositivo' => [
                'app_uuid'    => $this->app_uuid,
                'device_id'   => $this->device_id,
                'os_version'  => $this->os_version,
                'app_version' => $this->app_version,
            ],

            // ───────────────────────────────────────────────
            // Sincronización
            // ───────────────────────────────────────────────
            'synced'     => $this->synced,
            'synced_at'  => $this->synced_at?->toIso8601String(),

            // ───────────────────────────────────────────────
            // Blockchain
            // ───────────────────────────────────────────────
            'blockchain' => [
                'file_hash'        => $this->file_hash,
                'tx_hash'          => $this->tx_hash,
                'status'           => $this->blockchain_status,
                'verified_on_chain' => $this->verified_on_chain,
                'tx_url'           => $this->tx_hash
                    ? "https://sepolia.etherscan.io/tx/{$this->tx_hash}"
                    : null,
            ],

            // ───────────────────────────────────────────────
            // Timestamps
            // ───────────────────────────────────────────────
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
