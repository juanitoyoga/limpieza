<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contratista;
use App\Models\ContratoServicioDetalle;
use App\Models\EvidenciaHito;
use App\Models\HitoContratoServicio;
use App\Models\LogSistema;
use App\Models\MediaUpload;
use App\Services\LogSistemaService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HitoSyncController extends Controller
{
    /**
     * Sync batch: recibe uno o varios hitos (con sus evidencias)
     * capturados offline en la app móvil. Cada hito y cada evidencia
     * trae su propio uuid generado en el cliente, así que es seguro
     * reintentar el envío completo si la conexión se cae a medio sync —
     * nada se duplica.
     *
     * Las evidencias deben referenciar un media_uuid ya subido
     * previamente vía POST /api/media-uploads (paso 1 del flujo).
     *
     * Un contrato_servicio_detalle_id admite UN SOLO hito (índice único
     * real en la tabla) — el hito es el cierre de esa línea de servicio,
     * no un paso de una secuencia.
     *
     * Responde con el resultado por-item para que el cliente marque
     * localmente qué registros quedaron sincronizados y cuáles fallaron
     * (y por qué), sin perder todo el batch por un solo error.
     *
     * POST /api/sync/hitos
     * {
     *   "hitos": [
     *     {
     *       "uuid": "...",
     *       "contrato_servicio_detalle_id": 5,
     *       "descripcion_servicio": "Poda de árbol frente al parque",
     *       "capturado_en_campo_at": "2026-08-15T14:20:00-05:00",
     *       "evidencias": [
     *         { "uuid": "...", "tipo": "ANTES", "formato": "FOTO",
     *           "media_uuid": "...", "latitud": -0.18, "longitud": -78.47,
     *           "capturado_en_campo_at": "2026-08-15T14:20:00-05:00" }
     *       ]
     *     }
     *   ]
     * }
     */
    public function sync(Request $request)
    {
        $payload = $request->validate([
            'hitos'                                          => 'required|array|min:1',
            'hitos.*.uuid'                                    => 'required|uuid',
            'hitos.*.contrato_servicio_detalle_id'             => 'required|integer|exists:contrato_servicio_detalles,id',
            'hitos.*.descripcion_servicio'                     => 'nullable|string|max:255',
            'hitos.*.capturado_en_campo_at'                    => 'required|date',
            'hitos.*.evidencias'                               => 'array',
            'hitos.*.evidencias.*.uuid'                        => 'required|uuid',
            'hitos.*.evidencias.*.tipo'                        => 'required|in:ANTES,DESPUES',
            'hitos.*.evidencias.*.formato'                     => 'required|in:FOTO,VIDEO',
            'hitos.*.evidencias.*.descripcion'                 => 'nullable|string',
            'hitos.*.evidencias.*.media_uuid'                  => 'required|uuid',
            'hitos.*.evidencias.*.latitud'                     => 'nullable|numeric',
            'hitos.*.evidencias.*.longitud'                    => 'nullable|numeric',
            'hitos.*.evidencias.*.capturado_en_campo_at'       => 'required|date',
        ]);

        $userId = $request->user()->id;
        $resultados = [];

        foreach ($payload['hitos'] as $hitoData) {
            $resultados[] = $this->sincronizarHito($hitoData, $userId);
        }

        return response()->json(['data' => $resultados]);
    }

    private function sincronizarHito(array $hitoData, int $userId): array
    {
        try {
            return DB::transaction(function () use ($hitoData, $userId) {
                $detalle = ContratoServicioDetalle::with('contratoServicio')
                    ->findOrFail($hitoData['contrato_servicio_detalle_id']);

                $this->autorizarContratista($detalle, $userId);

                // Chequeo amistoso antes del insert: si este detalle ya
                // tiene un hito con OTRO uuid, el índice único de la BD
                // lo bloquearía igual, pero así devolvemos un mensaje
                // claro en vez de un error SQL crudo. withTrashed() porque
                // el índice único de MySQL no distingue filas soft-deleted
                // — si no se incluyen aquí, el pre-check no las ve pero el
                // INSERT sí choca contra ellas.
                $existente = HitoContratoServicio::withTrashed()
                    ->where('contrato_servicio_detalle_id', $detalle->id)
                    ->where('uuid', '!=', $hitoData['uuid'])
                    ->first();

                if ($existente) {
                    throw new \DomainException(
                        $existente->trashed()
                            ? "Este servicio ya tuvo un hito registrado y eliminado (uuid: {$existente->uuid}); contacta a soporte para restaurarlo o liberarlo antes de crear uno nuevo."
                            : "Este servicio ya tiene un hito registrado (uuid distinto: {$existente->uuid})."
                    );
                }

                $hito = HitoContratoServicio::updateOrCreate(
                    ['uuid' => $hitoData['uuid']],
                    [
                        'contratos_servicios_id'       => $detalle->contrato_servicio_id,
                        'contrato_servicio_detalle_id' => $detalle->id,
                        'descripcion_servicio'         => $hitoData['descripcion_servicio'] ?? null,
                        'user_id'                       => $userId,
                        'capturado_en_campo_at'         => $hitoData['capturado_en_campo_at'],
                        'sincronizado_at'               => now(),
                    ]
                );

                $evidenciasResultado = [];
                foreach ($hitoData['evidencias'] ?? [] as $evidenciaData) {
                    $evidenciasResultado[] = $this->sincronizarEvidencia($hito, $evidenciaData, $userId);
                }

                return [
                    'uuid'       => $hitoData['uuid'],
                    'status'     => 'ok',
                    'hito_id'    => $hito->id,
                    'evidencias' => $evidenciasResultado,
                ];
            });
        } catch (QueryException $e) {
            LogSistemaService::registrarExcepcion(
                origen: static::class,
                tipoOrigen: 'api_sync_hitos',
                e: $e,
                contexto: ['hito_uuid' => $hitoData['uuid']],
            );

            return [
                'uuid'    => $hitoData['uuid'],
                'status'  => 'error',
                'message' => 'Conflicto al guardar el hito (posible duplicado). Verifica e intenta de nuevo.',
            ];
        } catch (\Throwable $e) {
            LogSistemaService::registrar(
                origen: static::class,
                tipoOrigen: 'api_sync_hitos',
                nivel: LogSistema::NIVEL_ERROR,
                comentario: "Fallo sincronizando hito uuid={$hitoData['uuid']}",
                mensajeError: $e->getMessage(),
            );

            return [
                'uuid'    => $hitoData['uuid'],
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Aplica la secuencia de negocio del hito:
     *  1. Sin evidencias -> se acepta ANTES (foto/video + descripción).
     *  2. Con ANTES ya registrado -> el DESPUES solo lo puede registrar
     *     el MISMO usuario que registró el ANTES (continuidad: quien
     *     evaluó el estado inicial es quien certifica el trabajo hecho).
     *  3. Con ANTES y DESPUES -> el hito está terminado, no se acepta
     *     más evidencia (salvo reintento idempotente del mismo uuid ya
     *     guardado, que no cuenta como "nueva" evidencia).
     */
    private function sincronizarEvidencia(HitoContratoServicio $hito, array $data, int $userId): array
    {
        $media = MediaUpload::where('uuid', $data['media_uuid'])->first();

        if (! $media) {
            return [
                'uuid'    => $data['uuid'],
                'status'  => 'error',
                'message' => 'media_uuid no encontrado; sube el archivo primero vía POST /api/media-uploads',
            ];
        }

        $antes   = $hito->evidencias()->where('tipo', EvidenciaHito::TIPO_ANTES)->first();
        $despues = $hito->evidencias()->where('tipo', EvidenciaHito::TIPO_DESPUES)->first();

        $esReintentoDeExistente = ($antes && $antes->uuid === $data['uuid'])
            || ($despues && $despues->uuid === $data['uuid']);

        if ($antes && $despues && ! $esReintentoDeExistente) {
            return [
                'uuid'    => $data['uuid'],
                'status'  => 'error',
                'message' => 'Este hito ya está terminado (ANTES y DESPUES registrados); no se acepta más evidencia.',
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
                'hitos_contrato_servicio_id' => $hito->id,
                'tipo'                        => $data['tipo'],
                'formato'                     => $data['formato'],
                'descripcion'                 => $data['descripcion'] ?? null,
                'ruta_archivo'                => $media->ruta_archivo,
                'media_uuid'                  => $media->uuid,
                'hash_archivo'                => $media->hash_sha256, // ya calculado en el paso 1
                'latitud'                     => $data['latitud'] ?? null,
                'longitud'                    => $data['longitud'] ?? null,
                'user_id'                     => $userId,
                'capturado_en_campo_at'       => $data['capturado_en_campo_at'],
                'sincronizado_at'             => now(),
            ]
        );

        return ['uuid' => $data['uuid'], 'status' => 'ok'];
    }

    /**
     * Solo un contratista activo con asignación explícita a ESTE contrato
     * (no cualquier contrato de su proveedor) puede sincronizar hitos
     * contra ese contrato_servicio_detalle.
     */
    private function autorizarContratista(ContratoServicioDetalle $detalle, int $userId): void
    {
        $contratista = Contratista::where('user_id', $userId)->activos()->first();

        $tieneAcceso = $contratista && $contratista->tieneAccesoAContrato($detalle->contrato_servicio_id);

        abort_unless($tieneAcceso, 403, 'No autorizado para registrar hitos en este contrato.');
    }

    /**
     * Dirigente verifica el hito (requiere par ANTES/DESPUES completo).
     * Solo hace el update de negocio — HitoContratoServicioObserver
     * detecta el cambio en verificado_por y se encarga de calcular el
     * hash agregado, registrar el AuditEvent y publicar en blockchain.
     *
     * POST /api/hitos/{hito}/verificar
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
     *
     * ⚠️ El borrador original generaba aquí MovimientoServicio/OrdenPago
     * al cerrar el último hito del contrato — no incluí esa parte porque
     * no tengo esos modelos. Es un flujo aparte; lo armamos cuando toque.
     *
     * POST /api/hitos/{hito}/aprobar
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
            'id'                        => $hito->id,
            'uuid'                      => $hito->uuid,
            'estado'                    => $hito->estado,
            'verificado_at'             => $hito->verificado_at?->toIso8601String(),
            'aprobado_at'               => $hito->aprobado_at?->toIso8601String(),
            'blockchain_registrado_at'  => $hito->blockchain_registrado_at?->toIso8601String(),
        ];
    }
}
