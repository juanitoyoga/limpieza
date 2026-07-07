<?php

namespace App\Http\Controllers;

use App\Models\Multa;
use App\Models\AuditEvent;
use App\Http\Resources\MultaResource;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MultaController extends Controller
{
    /**
     * GET /api/multas
     * Listado con filtros: estado, vecino_id, barrio_id
     */
    public function index(Request $request)
    {
        $query = Multa::with('barrio')
            ->when($request->filled('estado'),    fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('vecino_id'),  fn($q) => $q->where('vecino_id', $request->vecino_id))
            ->when($request->filled('barrio_id'),  fn($q) => $q->where('barrio_id', $request->barrio_id))
            ->latest('fecha_emision');

        return MultaResource::collection($query->paginate(20));
    }

    /**
     * GET /api/multas/{multa}
     */
    public function show(Multa $multa): JsonResponse
    {
        $multa->load('barrio');
        return response()->json([
            'status' => 200,
            'data'   => new MultaResource($multa),
        ]);
    }

    /**
     * POST /api/multas/{multa}/pagar
     * Simula la API de pago del DMQ (ventanilla u online).
     * Sin restricción de rol por decisión de producto (simulación libre).
     */
    public function pagar(Request $request, Multa $multa): JsonResponse
    {
        $validated = $request->validate([
            'metodo_pago'      => 'required|string|in:ventanilla,online',
            'referencia_pago'  => 'nullable|string|max:100',
            'comprobante_pago' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in'       => 'El método de pago debe ser ventanilla u online.',
        ]);

        if ($multa->estaPagada()) {
            return response()->json([
                'status'  => 409,
                'error'   => 'ALREADY_PAID',
                'message' => 'Esta multa ya ha sido pagada.',
            ], 409);
        }

        if (!$multa->estaPendiente()) {
            return response()->json([
                'status'  => 422,
                'error'   => 'INVALID_STATE',
                'message' => 'Solo se pueden pagar multas en estado pendiente.',
            ], 422);
        }

        // Referencia de transacción simulada (equivalente al comprobante que devolvería el DMQ real)
        $referenciaPago = $validated['referencia_pago']
            ?? 'PAGO-' . strtoupper(Str::random(10));

        $comprobantePath = null;
        if ($request->hasFile('comprobante_pago')) {
            $comprobantePath = $request->file('comprobante_pago')->store('comprobantes_pago', 'public');
        }

        $multa->update([
            'estado'           => 'pagada',
            'metodo_pago'      => $validated['metodo_pago'],
            'referencia_pago'  => $referenciaPago,
            'comprobante_pago' => $comprobantePath,
            'fecha_pago'       => now(),
        ]);

        // ── Auditoría del pago + distribución ya ejecutada ────────
        $auditEvent = AuditEvent::logEvent(
            $multa,
            $request->user()->id,
            'multa_pagada',
            [
                'metodo_pago'      => $validated['metodo_pago'],
                'referencia_pago'  => $referenciaPago,
                'valor_multa'      => $multa->valor_multa,
                'valor_barrio'     => $multa->valor_barrio,
                'valor_municipio'  => $multa->valor_municipio,
                'valor_plataforma' => $multa->valor_plataforma,
            ]
        );

        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Pago registrado correctamente. Los fondos han sido distribuidos.',
            'data'    => new MultaResource($multa->fresh('barrio')),
        ]);
    }
}
