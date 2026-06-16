<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VecinoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            // Identificación y Llaves del Vecino
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'id_DMQ'        => $this->id_DMQ,
            'cedula'        => $this->cedula,

            // Información de Contacto (Nivel Vecino)
            'telefono'      => $this->telefono,

            // Fechas de Control Operativo
            'fecha_registro'    => $this->fecha_registro?->toIso8601String(),
            'fecha_cancelacion' => $this->fecha_cancelacion?->toIso8601String(),

            // Información de Perfil / Campos JSON (Casteados automáticamente a Array)
            'ocupacion'   => $this->ocupacion ?? [],
            'deportes'    => $this->deportes ?? [],
            'recreacion'  => $this->recreacion ?? [], // Reemplaza al antiguo 'hobby'

            // Dirección Estructurada
            'direccion' => [
                'calle_principal'  => $this->calle_principal,
                'numero'           => $this->numero,
                'calle_secundaria' => $this->calle_secundaria,
                'referencias'      => $this->referencias,
            ],

            // Estado del Vecino
            'is_active' => $this->is_active,

            // --- RELACIONES ---

            // Datos Personales Completos desde el Modelo User (Carga Condicional)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'               => $this->user->id,
                    'tipo_id'          => $this->user->tipo_id,
                    'nro_id'           => $this->user->nro_id,
                    'first_name'       => $this->user->first_name,
                    'last_name'        => $this->user->last_name,
                    'full_name'        => $this->user->full_name, // getFullNameAttribute()
                    'initials'         => $this->user->initials(),
                    'email'            => $this->user->email,
                    'phone'            => $this->user->phone, // Teléfono de la cuenta global de usuario
                    'birthdate'        => $this->user->birthdate?->toIso8601String(),
                    'gender'           => $this->user->gender,
                    'avatar_url'       => $this->user->avatar_url, // getAvatarUrlAttribute()
                    'role_name'        => $this->user->role_name,
                    'transition_role'  => $this->user->transition_role,
                    'is_active'        => $this->user->is_active,
                ];
            }),

            // Barrio asignado (Relación por id_DMQ)
            'barrio' => new BarrioResource($this->whenLoaded('barrio')),

            // Metadata relacionada (Opcional, si cuentas con un recurso específico)
            'metadata_vecinos' => $this->whenLoaded('metadata_vecinos'),

            // Timestamps del Registro
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
