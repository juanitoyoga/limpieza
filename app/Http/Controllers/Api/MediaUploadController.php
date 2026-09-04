<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaUpload;
use Illuminate\Http\Request;

class MediaUploadController extends Controller
{
    /**
     * Sube un archivo (foto/video) capturado en campo. Idempotente por
     * uuid: si el cliente reintenta el envío (ej. tras perder señal a
     * medio subir), retorna el registro existente sin duplicar el
     * archivo ni gastar almacenamiento de nuevo.
     *
     * POST /api/media-uploads  (multipart/form-data)
     *   - uuid (string, uuid, requerido)
     *   - archivo (file, requerido)
     *   - capturado_en_campo_at (datetime, requerido)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'uuid'                  => 'required|uuid',
            'archivo'               => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:51200', // 50MB
            'capturado_en_campo_at' => 'required|date',
        ]);

        $existente = MediaUpload::where('uuid', $data['uuid'])->first();
        if ($existente) {
            return response()->json(['data' => $existente], 200);
        }

        $archivo = $request->file('archivo');
        $ruta    = $archivo->store('evidencias-hitos', 'public');

        $media = MediaUpload::create([
            'uuid'                  => $data['uuid'],
            'user_id'               => $request->user()->id,
            'ruta_archivo'          => $ruta,
            'mime_type'             => $archivo->getMimeType(),
            'tamano_bytes'          => $archivo->getSize(),
            // Se calcula UNA vez aquí; HitoSyncController lo copia
            // directo a EvidenciaHito.hash_archivo — no hace falta
            // recalcularlo en el paso 2.
            'hash_sha256'           => hash_file('sha256', $archivo->getRealPath()),
            'capturado_en_campo_at' => $data['capturado_en_campo_at'],
        ]);

        return response()->json(['data' => $media], 201);
    }
}
