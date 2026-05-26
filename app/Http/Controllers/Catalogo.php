<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Actions\SyncCatalogoAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    /**
     * Retorna los ítems activos del catálogo filtrados por tipo.
     *
     * GET /api/catalogos?tipo=deporte
     * GET /api/catalogos?tipo=ocupacion
     * GET /api/catalogos?tipo=recreacion
     * GET /api/catalogos           → retorna los tres tipos juntos
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'tipo' => 'nullable|string|in:deporte,ocupacion,recreacion',
        ], [
            'tipo.in' => 'El tipo debe ser: deporte, ocupacion o recreacion.',
        ]);

        $query = Catalogo::where('esta_activo', true)
            ->orderBy('nombre');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $catalogos = $query->get(['id', 'external_id', 'nombre', 'tipo']);

        return response()->json([
            'success' => true,
            'tipo'    => $request->tipo ?? 'todos',
            'total'   => $catalogos->count(),
            'data'    => $catalogos,
        ]);
    }

    /**
     * Retorna los tres tipos agrupados en un solo llamado.
     * Útil para cargar todos los dropdowns de una vez en la app.
     *
     * GET /api/catalogos/agrupados
     */
    public function agrupados(): JsonResponse
    {
        $tipos = ['deporte', 'ocupacion', 'recreacion'];
        $resultado = [];

        foreach ($tipos as $tipo) {
            $resultado[$tipo] = Catalogo::where('esta_activo', true)
                ->where('tipo', $tipo)
                ->orderBy('nombre')
                ->get(['id', 'external_id', 'nombre'])
                ->values();
        }

        return response()->json([
            'success' => true,
            'data'    => $resultado,
        ]);
    }

    /**
     * Dispara la sincronización con Wikidata (solo admin/artisan).
     * POST /api/catalogos/sync   (protegido — solo rol ADMIN)
     */
    public function sync(): JsonResponse
    {
        try {
            $nuevos = (new SyncCatalogoAction())->execute(priorizarEcuador: true);

            return response()->json([
                'success' => true,
                'message' => "Sincronización completada. Nuevos ítems: {$nuevos}.",
                'nuevos'  => $nuevos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la sincronización: ' . $e->getMessage(),
            ], 500);
        }
    }
}
