<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreVecinoRequest;
use App\Models\Barrio;
use App\Models\Vecino;
use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VecinoController extends Controller
{
    public function __construct(private GeoService $geo) {}

    public function index()
    {
        return view('livewire.admin.vecinos.index');
    }

    /**
     * Activar/Desactivar — PATCH /barrios/{barrio}/toggle
     */
    public function toggle(Vecino $vecino)
    {
        $vecino->update(['is_active' => !$vecino->is_active]);

        return back()->with(
            'success',
            'Vecino ' . ($vecino->is_active ? 'activado' : 'desactivado') . ' correctamente.'
        );
    }

    public function list()
    {
        $vecinos = Vecino::orderBy('created_at', 'desc')->paginate(20);
        return view('livewire.admin.vecinos.list', compact('vecinos'));
    }

    /**
     * GET /api/vecinos/me
     * Devuelve si el usuario autenticado ya tiene vecino registrado.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role_name !== 'Vecino') {
            return response()->json(
                [
                    'has_vecino' => false,
                    'mensaje' => 'Solo los vecinos registrados pueden realizar denuncias.'
                ],
                200
            );
        }

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
        $user = $request->user();

        // Bloqueo por rol ya asignado (igual que Livewire\Admin\Vecinos\Create::mount())
        $rolesBloqueados = ['Vecino', 'Dirigente', 'Presidente', 'Funcionario', 'Supervisor', 'Auditor'];
        if (in_array($user->role_name, $rolesBloqueados)) {
            return response()->json([
                'message' => 'Tu cuenta ya cuenta con un perfil verificado en el sistema.'
            ], 409);
        }

        $existe = Vecino::where('user_id', $user->id)->exists();
        if ($existe) {
            return response()->json([
                'message' => 'El usuario ya tiene un vecino registrado.'
            ], 409);
        }

        $barrio = Barrio::where('id_DMQ', $request->barrio_id_DMQ)->firstOrFail();
        \Log::info('DEBUG vecino store', [
            'barrio_id_DMQ_recibido' => $request->barrio_id_DMQ,
            'barrio_encontrado_id'   => $barrio->id,
            'barrio_encontrado_dmq'  => $barrio->id_DMQ,
            'barrio_nombre'          => $barrio->nombre,
            'polygon_usado'          => $barrio->polygon,
            'lat_recibida'           => $request->latitud,
            'lng_recibida'           => $request->longitud,
        ]);
        // Validación geoespacial Ray Casting (igual que Livewire\Admin\Vecinos\Create::save())
        if (!empty($barrio->polygon)) {
            $dentroDePoligono = $this->geo->pointInPolygon(
                $request->latitud,
                $request->longitud,
                $barrio->polygon
            );

            if (!$dentroDePoligono) {
                return response()->json([
                    'message' => 'La ubicación de tu domicilio se encuentra fuera del límite perimetral de este barrio.',
                    'errors'  => ['barrio_id_DMQ' => ['Ubicación fuera del polígono del barrio.']],
                ], 422);
            }
        }

        $vecino = DB::transaction(function () use ($request, $user) {
            $vecino = Vecino::create([
                'user_id'          => $user->id,
                'id_DMQ'           => $request->barrio_id_DMQ,
                'cedula'           => $user->nro_id, // heredada del registro base (User ya validado con tipo_id CÉDULA)
                'telefono'         => $request->telefono ?: $user->phone,
                'calle_principal'  => $request->calle_principal,
                'numero'           => $request->numero,
                'calle_secundaria' => $request->calle_secundaria,
                'referencias'      => $request->referencias,
                'fecha_registro'   => now(),
                'is_active'        => true,
                'ocupacion'        => $request->ocupacion ?? [],
                'deportes'         => $request->deportes ?? [],
                'recreacion'       => $request->recreacion ?? [],
            ]);

            $user->update(['role_name' => 'Vecino']);

            return $vecino;
        });

        return response()->json([
            'message'   => '¡Perfil verificado! Ahora eres formalmente Vecino de tu rincón.',
            'vecino'    => $vecino->load('barrio'),
            'role_name' => 'Vecino',
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

        $user = $request->user();
        if ($user->role_name !== 'Vecino') {
            return response()->json([
                'valido'  => false,
                'barrio_nombre' => null,
                'mensaje' => 'Solo los vecinos registrados pueden realizar denuncias.',
            ], 200);
        }
        $vecino = Vecino::with('barrio')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        if (!$vecino->barrio) {
            return response()->json([
                'valido'  => false,
                'barrio_nombre' => null,
                'mensaje' => 'Vecino sin barrio asignado, ubicación no válida.',
            ], 200);
        }
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
