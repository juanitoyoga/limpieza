<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contratista;
use App\Models\ContratoServicio;
use App\Models\ContratoServicioDetalle;
use Illuminate\Http\Request;

class ContratoServicioContratistaController extends Controller
{
    /**
     * Contratos donde el usuario autenticado tiene una asignación activa
     * como contratista — no todos los contratos del proveedor, solo los
     * que le fueron asignados explícitamente (AsignacionContratoServicio).
     *
     * GET /api/mis-contratos-servicios
     */
    public function index(Request $request)
    {
        $contratista = Contratista::where('user_id', $request->user()->id)
            ->activos()
            ->first();

        if (! $contratista) {
            return response()->json(['data' => []]);
        }

        $contratoIds = $contratista->asignaciones()->activas()->pluck('contrato_servicio_id');

        $contratos = ContratoServicio::whereIn('id', $contratoIds)
            ->where('auth_status', ContratoServicio::ESTADO_APROBADA)
            ->get()
            ->map(fn(ContratoServicio $c) => [
                'id'                 => $c->id,
                'codigo'             => $c->codigo,
                'titulo'             => $c->titulo,
                'auth_status'        => $c->auth_status,
                'fecha_inicio'       => $c->fecha_inicio?->toDateString(),
                'fecha_fin_estimada' => $c->fecha_fin_estimada?->toDateString(),
            ]);

        return response()->json(['data' => $contratos]);
    }

    /**
     * Líneas de servicio (detalles) de un contrato específico — SOLO las
     * que todavía necesitan trabajo del contratista. Un servicio con
     * ejecución completa (ANTES + DESPUES ya registrados) desaparece de
     * esta lista: ya no hay nada que la app deba hacer con él, el
     * siguiente paso (iniciar verificación) es del Dirigente en el panel
     * web, no de la app móvil.
     *
     * GET /api/contratos-servicios/{contrato}/detalles
     *
     * NOTA: 'nombre' de CatalogoServicios queda con '?? null' porque no
     * tengo ese modelo para confirmar el nombre real de la columna.
     */
    public function detalles(Request $request, ContratoServicio $contrato)
    {
        $this->autorizarAcceso($request->user()->id, $contrato);

        $userId = $request->user()->id;

        $detalles = ContratoServicioDetalle::where('contrato_servicio_id', $contrato->id)
            ->with(['catalogoServicio', 'evidenciasHito'])
            ->get()
            ->reject(fn(ContratoServicioDetalle $d) => $d->ejecucionCompleta())
            ->map(function (ContratoServicioDetalle $d) use ($userId) {
                $antes = $d->evidenciaAntes();

                return [
                    'id'                => $d->id,
                    'cantidad'          => $d->cantidad,
                    'costo_unitario'    => $d->costo_unitario,
                    'subtotal'          => $d->subtotal,
                    'catalogo_servicio' => $d->catalogoServicio ? [
                        'id'     => $d->catalogoServicio->id,
                        'nombre' => $d->catalogoServicio->nombre ?? null,
                    ] : null,
                    // null = todavía no hay ANTES, la app debe ofrecer
                    // capturarlo. No-null = ya hay ANTES, falta DESPUES.
                    'evidencia_antes' => $antes ? [
                        'id'          => $antes->id,
                        'uuid'        => $antes->uuid,
                        'descripcion' => $antes->descripcion,
                    ] : null,
                    // true solo si el ANTES lo registró ESTE MISMO
                    // usuario — misma regla que valida
                    // EvidenciaSyncController::sincronizarEvidencia() del
                    // lado del servidor. Se expone aquí para que la app
                    // deshabilite el botón antes de intentar el sync.
                    'puede_registrar_despues' => $antes !== null && $antes->user_id === $userId,
                ];
            })
            ->values();

        return response()->json(['data' => $detalles]);
    }

    private function autorizarAcceso(int $userId, ContratoServicio $contrato): void
    {
        $contratista = Contratista::where('user_id', $userId)->activos()->first();

        $tieneAcceso = $contratista && $contratista->tieneAccesoAContrato($contrato->id);

        abort_unless($tieneAcceso, 403, 'No autorizado para este contrato.');
    }
}
