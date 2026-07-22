<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificacionResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            // ───────────────────────────────────────────────
            // Identificación
            // ───────────────────────────────────────────────
            'id'                  => $this->id,
            'estado'               => $this->estado,
            'estado_label'         => $this->estadoLabel(),
            'fecha_notificacion'   => $this->fecha_notificacion?->toIso8601String(),
            'fecha_vencimiento'    => $this->fecha_vencimiento?->toIso8601String(),

            // ───────────────────────────────────────────────
            // Relaciones
            // ───────────────────────────────────────────────
            'denuncia' => $this->whenLoaded('denuncia', function () {
                return [
                    'id'     => $this->denuncia->id,
                    'estado' => $this->denuncia->estado,
                ];
            }),

            'user' => $this->whenLoaded('user', function () {
                return $this->user ? [
                    'id'     => $this->user->id,
                    'nombre' => $this->user->first_name . ' ' . $this->user->last_name,
                ] : null;
            }),

            'barrio' => $this->whenLoaded('barrio', function () {
                return $this->barrio ? [
                    'id'     => $this->barrio->id,
                    'id_DMQ' => $this->barrio->id_DMQ,
                    'nombre' => $this->barrio->nombre,
                ] : null;
            }),

            'ordenanza'      => new Ordenanza332Resource($this->whenLoaded('ordenanza332')),
            'barrio_atributo' => $this->whenLoaded('barrioAtributo', function () {
                return [
                    'id'           => $this->barrioAtributo->id,
                    'plazo_horas'  => $this->barrioAtributo->plazo_horas,
                ];
            }),

            // Datos de auditoría dinámica usando el accessor del modelo
            'auditoria_revisor' => $this->revisor,

            // ───────────────────────────────────────────────
            // Datos del predio / contribuyente (snapshot)
            // ───────────────────────────────────────────────
            'numero_predio' => $this->numero_predio,
            'contribuyente' => [
                'nombre'         => $this->contribuyente_nombre,
                'identificacion' => $this->contribuyente_identificacion,
                'email'          => $this->contribuyente_email,
                'telefono'       => $this->contribuyente_telefono,
                'direccion'      => $this->contribuyente_direccion,
            ],

            // ───────────────────────────────────────────────
            // Envío
            // ───────────────────────────────────────────────
            'envio' => [
                'medio'       => $this->medio,
                'medio_label' => $this->medioLabel(),
                'medio_icon'  => $this->medioIcon(),
                'enviada_at'  => $this->enviada_at?->toIso8601String(),
                'codigo_envio' => $this->codigo_envio,
                'error_envio'  => $this->error_envio,
            ],

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
            // Observaciones
            // ───────────────────────────────────────────────
            'observacion'    => $this->observacion,
            'motivo_rechazo' => $this->motivo_rechazo,

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
            'synced'    => $this->synced,
            'synced_at' => $this->synced_at?->toIso8601String(),

            // ───────────────────────────────────────────────
            // Blockchain
            // ───────────────────────────────────────────────
            'blockchain' => [
                'file_hash'         => $this->file_hash,
                'tx_hash'           => $this->tx_hash,
                'status'            => $this->blockchain_status,
                'verified_on_chain' => $this->verified_on_chain,
                'tx_url'            => $this->tx_hash
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
