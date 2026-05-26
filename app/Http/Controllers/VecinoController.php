<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVecinoRequest;
use App\Models\Barrio;
use App\Models\Vecino;
use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VecinoController extends Controller
{
    public function __construct(private GeoService $geo) {}

    /**
     * GET /api/vecinos/me
     * Devuelve si el usuario autenticado ya tiene vecino registrado.
     */
    public function me(Request $request): JsonResponse
    {
        $vecino = Vecino::with('barrio')
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$vecino) {
            return response()->json(['has_vecino' => false], 200);
        }

        return response()->json([
            'has_vecino' => true,
            'vecino' => [
                'id'               => $vecino->id,
                'cedula'           => $vecino->cedula,
                'telefono'         => $vecino->telefono,
                'calle_principal'  => $vecino->calle_principal,
                'numero'           => $vecino->numero,
                'calle_secundaria' => $vecino->calle_secundaria,
                'referencias'      => $vecino->referencias,
                'barrio_id_DMQ'    => $vecino->id_DMQ,
                'barrio_nombre'    => $vecino->barrio?->nombre,
                'barrio_polygon'   => $vecino->barrio?->polygon,
            ]
        ]);
    }

    /**
     * POST /api/vecinos
     * Crea el vecino para el usuario autenticado.
     */
    public function store(StoreVecinoRequest $request): JsonResponse
    {
        // Evitar duplicados
        $existe = Vecino::where('user_id', $request->user()->id)->exists();
        if ($existe) {
            return response()->json([
                'message' => 'El usuario ya tiene un vecino registrado.'
            ], 409);
        }

        $vecino = Vecino::create([
            'user_id'          => $request->user()->id,
            'userroles_id'     => $request->userroles_id,
            'id_DMQ'           => $request->barrio_id_DMQ,
            'cedula'           => $request->cedula,
            'telefono'         => $request->telefono,
            'calle_principal'  => $request->calle_principal,
            'numero'           => $request->numero,
            'calle_secundaria' => $request->calle_secundaria,
            'referencias'      => $request->referencias,
            'fecha_registro'   => now()->toDateString(),
            'is_active'        => true,
        ]);

        return response()->json([
            'message' => 'Vecino registrado correctamente.',
            'vecino'  => $vecino->load('barrio'),
        ], 201);
    }

    /**
     * POST /api/vecinos/validar-ubicacion
     * Kotlin envía GPS — Laravel valida que esté dentro del barrio del vecino.
     */
    public function validarUbicacion(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $vecino = Vecino::with('barrio')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $polygon = $vecino->barrio?->polygon;

        if (empty($polygon)) {
            return response()->json([
                'valido'  => true,
                'mensaje' => 'Barrio sin polígono definido, ubicación aceptada.',
            ]);
        }

        $dentroDeBarrio = $this->geo->pointInPolygon(
            $request->lat,
            $request->lng,
            $polygon
        );

        return response()->json([
            'valido'        => $dentroDeBarrio,
            'barrio_nombre' => $vecino->barrio->nombre,
            'mensaje'       => $dentroDeBarrio
                ? 'Ubicación válida dentro de tu barrio.'
                : 'La denuncia debe realizarse dentro de tu barrio: ' . $vecino->barrio->nombre,
        ]);
    }
}
