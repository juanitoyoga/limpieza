<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DirigenteResource extends JsonResource
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
            // Identificación y Llaves del Dirigente
            'id'            => $this->id,
            'user_id'       => $this->user_id,
            'userrole_id'   => $this->userrole_id,
            'id_DMQ'        => $this->id_DMQ,

            // Credenciales y Seguridad (Nivel Dirigente)
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'two_factor_enabled'=> $this->tieneDosFactor(),

            // Configuración y Estado del Dirigente
            'timezone'  => $this->timezone,
            'language'  => $this->language,
            'is_active' => $this->is_active,

            // Información de Contacto
            'phone'              => $this->phone,
            'phone_formatted'    => $this->telefono_formateado, 

            // Dirección Completa
            'direccion' => [
                'calle_principal'  => $this->calle_principal,
                'numero'           => $this->numero,
                'calle_secundaria' => $this->calle_secundaria,
                'referencias'      => $this->referencias,
                'completa'         => $this->direccion_completa, 
            ],

            // Auditoría de Sesión
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'last_login_ip' => $this->last_login_ip,

            // --- RELACIONES ---

            // Datos Personales Completos desde el Modelo User (Carga Condicional)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'               => $this->user->id,
                    'tipo_id'          => $this->user->tipo_id,
                    'nro_id'           => $this->user->nro_id, // Identificación / Cédula
                    'first_name'       => $this->user->first_name,
                    'last_name'        => $this->user->last_name,
                    'full_name'        => $this->user->full_name, // Accessor corregido (getFullNameAttribute)
                    'initials'         => $this->user->initials(),
                    'email'            => $this->user->email,
                    'phone'            => $this->user->phone,
                    'birthdate'        => $this->user->birthdate?->toIso8601String(),
                    'gender'           => $this->user->gender,
                    'avatar_url'       => $this->user->avatar_url, // Accessor (getAvatarUrlAttribute)
                    'role_name'        => $this->user->role_name,
                    'transition_role'  => $this->user->transition_role,
                    'is_active'        => $this->user->is_active,
                ];
            }),

            // Rol de Usuario del Dirigente
            'userrole' => $this->whenLoaded('userrole'),

            // Barrio asignado (Relación por id_DMQ)
            'barrio' => new BarrioResource($this->whenLoaded('barrio')),

            // Timestamps de Control
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'deleted_at' => $this->deleted_at?->toIso8601String(),
        ];
    }
}
