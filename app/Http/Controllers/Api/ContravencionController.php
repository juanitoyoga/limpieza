<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ordenanza332;
use Illuminate\Http\JsonResponse;

class ContravencionController extends Controller
{
    /**
     * GET /api/contravenciones
     * Devuelve el catálogo completo para sincronizar en Room.
     */
    public function index(): JsonResponse
    {
        $contravenciones = Ordenanza332::select(
            'id',
            'codigo',
            'nombre',
            'descripcion',
            'tipo',
            'nivel_gravedad'
        )
            ->orderBy('codigo')
            ->get();

        return response()->json($contravenciones);
    }
}
