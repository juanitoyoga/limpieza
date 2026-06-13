<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\Ordenanza332;
use App\Services\GeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DenunciaController extends Controller
{
    public function __construct(private GeoService $geoService) {}

    public function store(Request $request)
    {
        $request->validate([
            'latitud'          => 'required|numeric|between:-90,90',
            'longitud'         => 'required|numeric|between:-180,180',
            'ordenanza332_id'  => 'required|integer',
            'descripcion'      => 'nullable|string|max:1000',
            'evidencia'        => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:51200',
            // Metadatos del dispositivo (opcionales)
            'app_uuid'         => 'nullable|uuid',
            'device_id'        => 'nullable|string|max:64',
            'os_version'       => 'nullable|string|max:32',
            'app_version'      => 'nullable|string|max:16',
        ]);

        // ── 1. Vecino autenticado → barrio ────────────────────────
        $user   = $request->user();
        $vecino = $user->vecino()->with('barrio')->first();

        if (!$vecino) {
            return response()->json([
                'message' => 'El usuario no tiene un perfil de vecino registrado.',
            ], 403);
        }

        $barrio = $vecino->barrio;

        if (!$barrio || !$barrio->activo) {
            return response()->json([
                'message' => 'No se encontró un barrio activo asociado a tu cuenta.',
            ], 422);
        }

        if (empty($barrio->polygon)) {
            return response()->json([
                'message' => 'El barrio aún no tiene polígono registrado.',
            ], 422);
        }

        // ── 2. Contravención válida en catálogo ───────────────────
        $ordenanza = Ordenanza332::find($request->ordenanza332_id);

        if (!$ordenanza) {
            return response()->json([
                'message' => 'La contravención seleccionada no existe en el catálogo.',
            ], 422);
        }

        // ── 3. GPS dentro del polígono del barrio ─────────────────
        $dentroDelBarrio = $this->geoService->pointInPolygon(
            lat: $request->latitud,
            lng: $request->longitud,
            polygon: $barrio->polygon   // ya es array gracias al cast 'json'
        );

        if (!$dentroDelBarrio) {
            return response()->json([
                'message' => 'La ubicación registrada está fuera de los límites de tu barrio.',
                'codigo'  => 'FUERA_DE_BARRIO',
                'barrio'  => $barrio->nombre,
            ], 422);
        }

        // ── 4. Guardar evidencia + crear denuncia ─────────────────
        $denuncia = DB::transaction(function () use ($request, $vecino, $ordenanza) {
            $archivo   = $request->file('evidencia');
            $mimeType  = $archivo->getMimeType();
            $esFoto    = str_starts_with($mimeType, 'image');
            $path      = $archivo->store("denuncias/{$vecino->id}", 'public');
            $fileHash  = hash_file('sha256', $archivo->getRealPath());

            return Denuncia::create([
                'vecino_id'        => $vecino->id,
                'ordenanza332_id'  => $ordenanza->id,
                'latitud'          => $request->latitud,
                'longitud'         => $request->longitud,
                'descripcion'      => $request->descripcion,
                'fecha_denuncia'   => now(),
                'estado'           => 'pendiente',
                'evidencia_path'   => $path,
                'evidencia_tipo'   => $esFoto ? 'foto' : 'video',
                'file_hash'        => $fileHash,   // listo para blockchain
                'app_uuid'         => $request->app_uuid ?? Str::uuid(),
                'device_id'        => $request->device_id,
                'os_version'       => $request->os_version,
                'app_version'      => $request->app_version,
                'synced'           => true,
                'synced_at'        => now(),
            ]);
        });

        return response()->json([
            'message'      => 'Denuncia registrada exitosamente.',
            'denuncia_id'  => $denuncia->id,
            'estado'       => $denuncia->estado,
            'evidencia_url' => $denuncia->evidencia_url,  // accessor del modelo
        ], 201);
    }
}
