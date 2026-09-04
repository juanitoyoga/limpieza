<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contratista;
use App\Models\ContratoServicioDetalle;
use App\Models\EvidenciaHito;
use App\Models\LogSistema;
use App\Models\MediaUpload;
use App\Services\LogSistemaService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvidenciaSyncController extends Controller
{
    /**
     * Sync batch de evidencias (ANTES/DESPUES) capturadas offline en la
     * app móvil. Cada evidencia se vincula DIRECTO al
     * contrato_servicio_detalle_id — el Hito todavía no existe en este
     * punto, nace recién cuando el Dirigente inicia la verificación
     * (ver IniciarVerificacionController).
     *
     * Secuencia validada por servicio (contrato_servicio_detalle_id):
     *  1. Sin evidencias -> se acepta ANTES.
     *  2. Con ANTES -> el DESPUES solo lo acepta el MISMO usuario que
     *     registró el ANTES.
     *  3. Con ANTES y DESPUES -> "ejecución completa", no se acepta más
     *     evidencia (el índice único de BD lo bloquea igual).
     *
     * POST /api/sync/evidencias
     * {
     *   "evidencias": [
     *     { "uuid": "...", "contrato_servicio_detalle_id": 5,
     *       "tipo": "ANTES", "formato": "FOTO", "descripcion": "...",
     *       "media_uuid": "...", "latitud": -0.18, "longitud": -78.47,
     *       "capturado_en_campo_at": "2026-08-17T09:10:14-05:00" }
     *   ]
     * }
     */
    public function sync(Request $request)
    {
        $payload = $request->validate([
            'evidencias'                               => 'required|array|min:1',
            'evidencias.*.uuid'                          => 'required|uuid',
            'evidencias.*.contrato_servicio_detalle_id'  => 'required|integer|exists:contrato_servicio_detalles,id',
            'evidencias.*.tipo'                          => 'required|in:ANTES,DESPUES',
            'evidencias.*.formato'                       => 'required|in:FOTO,VIDEO',
            'evidencias.*.descripcion'                   => 'nullable|string',
            'evidencias.*.media_uuid'                    => 'required|uuid',
            'evidencias.*.latitud'                       => 'nullable|numeric',
            'evidencias.*.longitud'                      => 'nullable|numeric',
            'evidencias.*.capturado_en_campo_at'         => 'required|date',
        ]);

        $userId = $request->user()->id;
        $resultados = [];

        foreach ($payload['evidencias'] as $data) {
            $resultados[] = $this->sincronizarEvidencia($data, $userId);
        }

        return response()->json(['data' => $resultados]);
    }

    private function sincronizarEvidencia(array $data, int $userId): array
    {
        try {
            return DB::transaction(function () use ($data, $userId) {
                $detalle = ContratoServicioDetalle::with(['contratoServicio', 'evidenciasHito'])
                    ->findOrFail($data['contrato_servicio_detalle_id']);

                $this->autorizarContratista($detalle, $userId);

                $media = MediaUpload::where('uuid', $data['media_uuid'])->first();
                if (! $media) {
                    return [
                        'uuid'    => $data['uuid'],
                        'status'  => 'error',
                        'message' => 'media_uuid no encontrado; sube el archivo primero vía POST /api/media-uploads',
                    ];
                }

                $antes   = $detalle->evidenciaAntes();
                $despues = $detalle->evidenciaDespues();

                $esReintentoDeExistente = ($antes && $antes->uuid === $data['uuid'])
                    || ($despues && $despues->uuid === $data['uuid']);

                if ($antes && $despues && ! $esReintentoDeExistente) {
                    return [
                        'uuid'    => $data['uuid'],
                        'status'  => 'error',
                        'message' => 'Este servicio ya tiene ejecución completa (ANTES y DESPUES registrados); no se acepta más evidencia.',
                    ];
                }

                if ($data['tipo'] === EvidenciaHito::TIPO_DESPUES && $antes && $antes->user_id !== $userId) {
                    return [
                        'uuid'    => $data['uuid'],
                        'status'  => 'error',
                        'message' => 'Solo el contratista que registró la evidencia ANTES de este servicio puede registrar el DESPUES.',
                    ];
                }

                EvidenciaHito::updateOrCreate(
                    ['uuid' => $data['uuid']],
                    [
                        'contrato_servicio_detalle_id' => $detalle->id,
                        'tipo'                          => $data['tipo'],
                        'formato'                       => $data['formato'],
                        'descripcion'                   => $data['descripcion'] ?? null,
                        'ruta_archivo'                  => $media->ruta_archivo,
                        'media_uuid'                    => $media->uuid,
                        'hash_archivo'                  => $media->hash_sha256,
                        'latitud'                       => $data['latitud'] ?? null,
                        'longitud'                      => $data['longitud'] ?? null,
                        'user_id'                       => $userId,
                        'capturado_en_campo_at'         => $data['capturado_en_campo_at'],
                        'sincronizado_at'               => now(),
                    ]
                );

                return ['uuid' => $data['uuid'], 'status' => 'ok'];
            });
        } catch (QueryException $e) {
            LogSistemaService::registrarExcepcion(
                origen: static::class,
                tipoOrigen: 'api_sync_evidencias',
                e: $e,
                contexto: ['evidencia_uuid' => $data['uuid']],
            );

            return [
                'uuid'    => $data['uuid'],
                'status'  => 'error',
                'message' => 'Conflicto al guardar la evidencia (posible duplicado). Verifica e intenta de nuevo.',
            ];
        } catch (\Throwable $e) {
            LogSistemaService::registrar(
                origen: static::class,
                tipoOrigen: 'api_sync_evidencias',
                nivel: LogSistema::NIVEL_ERROR,
                comentario: "Fallo sincronizando evidencia uuid={$data['uuid']}",
                mensajeError: $e->getMessage(),
            );

            return [
                'uuid'    => $data['uuid'],
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Solo un contratista activo con asignación explícita al contrato de
     * este detalle puede registrar evidencia contra él.
     */
    private function autorizarContratista(ContratoServicioDetalle $detalle, int $userId): void
    {
        $contratista = Contratista::where('user_id', $userId)->activos()->first();

        $tieneAcceso = $contratista && $contratista->tieneAccesoAContrato($detalle->contrato_servicio_id);

        abort_unless($tieneAcceso, 403, 'No autorizado para registrar evidencia en este contrato.');
    }
}
