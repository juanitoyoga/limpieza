<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDenunciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Si usas auth(), aquí puedes validar roles
    }

    public function rules(): array
    {
        return [

            // ───────────────────────────────────────────────
            // Relaciones principales
            // ───────────────────────────────────────────────
            'vecino_id'        => 'required|exists:vecinos,id',
            'ordenanza332_id'  => 'required|exists:ordenanza332,id',
            'dirigente_id'     => 'nullable|exists:dirigentes,id',
            'funcionario_id'   => 'nullable|exists:funcionarios,id',

            // ───────────────────────────────────────────────
            // Datos de la denuncia
            // ───────────────────────────────────────────────
            'direccion'        => 'nullable|string|max:255',
            'descripcion'      => 'nullable|string|max:5000',
            'estado'           => 'nullable|string|in:pendiente,en_proceso,resuelto,rechazado',
            'multa_calculada'  => 'nullable|numeric|min:0',

            // ───────────────────────────────────────────────
            // Evidencia (foto o video)
            // ───────────────────────────────────────────────
            'evidencia'        => 'nullable|file|max:20480|mimes:jpg,jpeg,png,mp4,mov',

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
            // Sincronización móvil
            // ───────────────────────────────────────────────
            'synced'           => 'nullable|boolean',
            'synced_at'        => 'nullable|date',

            // ───────────────────────────────────────────────
            // Blockchain (el backend genera el hash real)
            // ───────────────────────────────────────────────
            'file_hash'        => 'nullable|string|max:255',
            'tx_hash'          => 'nullable|string|max:255',
            'blockchain_status' => 'nullable|string|in:pending,confirmed,failed',
            'verified_on_chain' => 'nullable|boolean',
        ];
    }
}
