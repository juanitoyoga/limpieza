<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVecinoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo poseedores de CÉDULA pueden activarse como Vecino
        return $this->user()?->tipo_id === 'Cedula';
    }

    public function rules(): array
    {
        return [
            'barrio_id_DMQ'    => 'required|exists:barrios,id_DMQ',
            'telefono'         => 'nullable|string|max:15',
            'calle_principal'  => 'required|string|max:100',
            'numero'           => 'required|string|max:10',
            'calle_secundaria' => 'required|string|max:100',
            'referencias'      => 'nullable|string|max:255',
            'latitud'          => 'required|numeric|between:-90,90',
            'longitud'         => 'required|numeric|between:-180,180',
            'ocupacion'        => 'nullable|array',
            'deportes'         => 'nullable|array',
            'recreacion'       => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'barrio_id_DMQ.exists' => 'El barrio seleccionado no existe.',
            'latitud.required'     => 'Debes capturar tu ubicación GPS.',
            'longitud.required'    => 'Debes capturar tu ubicación GPS.',
        ];
    }

    public function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Solo los usuarios registrados con Cédula de Identidad pueden activarse como Vecino.'
        );
    }
}
