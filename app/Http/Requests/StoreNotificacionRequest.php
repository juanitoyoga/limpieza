<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificacionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [

            // ───────────────────────────────────────────────
            // Evidencia (foto o video)
            // ───────────────────────────────────────────────
            'evidencia'        => 'required|file|max:20480|mimes:jpg,jpeg,png,mp4,mov',

            // ───────────────────────────────────────────────
            // Geolocalización
            // ───────────────────────────────────────────────
            'latitud'          => 'required|numeric|between:-90,90',
            'longitud'         => 'required|numeric|between:-180,180',

            // ───────────────────────────────────────────────
            // Metadatos del dispositivo
            // ───────────────────────────────────────────────
            'app_uuid'         => 'nullable|string|max:255',
            'device_id'        => 'required|string|max:255',
            'os_version'       => 'required|string|max:255',
            'app_version'      => 'required|string|max:255',

            // ───────────────────────────────────────────────
            // Blockchain (hash ya calculado desde el frontend)
            // ───────────────────────────────────────────────
            'evidencia_hash'   => ['required', 'string', 'size:64', 'regex:/^[a-f0-9]+$/i'],
        ];
    }

    public function messages(): array
    {
        return [
            'evidencia.required'      => 'Debes adjuntar una foto o video como evidencia.',
            'evidencia.mimes'         => 'La evidencia debe ser una imagen (jpg, jpeg, png) o un video (mp4).',
            'evidencia.max'           => 'El archivo de evidencia no debe superar los 20 MB.',
            'evidencia_hash.required' => 'Falta el hash de integridad de la evidencia.',
            'evidencia_hash.size'     => 'El hash de integridad no tiene el formato esperado.',
            'latitud.required'       => 'La ubicación (latitud) es obligatoria.',
            'longitud.required'      => 'La ubicación (longitud) es obligatoria.',
        ];
    }
}
