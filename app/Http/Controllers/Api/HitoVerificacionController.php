<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HitoContratoServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HitoVerificacionController extends Controller
{
    /**
     * Dirigente verifica el hito (requiere par ANTES/DESPUES completo —
     * garantizado siempre que exista, porque el Hito solo se crea vía
     * IniciarVerificacionController cuando ya hay ambas evidencias).
     * Solo hace el update de negocio — HitoContratoServicioObserver
     * detecta el cambio en verificado_por y se encarga de calcular el
     * hash agregado, registrar el AuditEvent y publicar en blockchain.
     */
    public function verificar(Request $request, HitoContratoServicio $hito)
    {
        Gate::authorize('verificar-hito', $hito);

        if (! $hito->tieneParCompleto()) {
            return response()->json(['message' => 'Faltan evidencias ANTES/DESPUES'], 422);
        }

        $hito->update([
            'verificado_por' => $request->user()->id,
            'verificado_at'  => now(),
        ]);

        return response()->json(['data' => $this->serializarHito($hito->fresh())]);
    }

    /**
     * Presidente aprueba el hito ya verificado. Igual que verificar():
     * solo el update de negocio, el Observer maneja blockchain/auditoría.
     */
    public function aprobar(Request $request, HitoContratoServicio $hito)
    {
        Gate::authorize('aprobar-hito', $hito);

        if (! $hito->estaVerificado()) {
            return response()->json(['message' => 'El hito debe estar verificado antes de aprobarse'], 422);
        }

        $hito->update([
            'aprobado_por' => $request->user()->id,
            'aprobado_at'  => now(),
        ]);

        return response()->json(['data' => $this->serializarHito($hito->fresh())]);
    }

    private function serializarHito(HitoContratoServicio $hito): array
    {
        return [
            'id'                       => $hito->id,
            'uuid'                     => $hito->uuid,
            'estado'                   => $hito->estado,
            'verificado_at'            => $hito->verificado_at?->toIso8601String(),
            'aprobado_at'              => $hito->aprobado_at?->toIso8601String(),
            'blockchain_registrado_at' => $hito->blockchain_registrado_at?->toIso8601String(),
        ];
    }
}
