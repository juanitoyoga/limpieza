<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreVecinoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barrio_id_DMQ'    => 'required|exists:barrios,id_DMQ',
            'userroles_id'     => 'required|exists:user_roles,id',
            'cedula'           => 'required|string|size:10|unique:vecinos,cedula',
            'telefono'         => 'nullable|string|max:15',
            'calle_principal'  => 'required|string|max:100',
            'numero'           => 'required|string|max:10',
            'calle_secundaria' => 'required|string|max:100',
            'referencias'      => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'cedula.size'           => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.unique'         => 'Esta cédula ya está registrada.',
            'barrio_id_DMQ.exists'  => 'El barrio seleccionado no existe.',
            'userroles_id.exists'   => 'El rol especificado no existe.',
        ];
    }
}
