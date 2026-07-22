<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificacionEvidenciaRequest;
use App\Http\Resources\NotificacionResource;
use App\Services\GeoService;
use App\Models\Notificacion;
use App\Models\Denuncia;
use App\Models\Funcionario;
use App\Models\Supervisor;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NotificacionController extends Controller
{
    private const MAX_INTENTOS = 3;
    private const COOLDOWN_MINUTOS = 5;

    public function index(Request $request)
    {
        $user = auth()->user();

        $tieneFiltros = $request->hasAny(['estado', 'barrio_id', 'fecha_desde', 'fecha_hasta']);

        $query = Notificacion::with(['user', 'barrio', 'ordenanza332'])
            // ── Fallback: sin filtros → notificaciones propias del último mes ──
            ->when(!$tieneFiltros, function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('fecha_notificacion', '>=', now()->subMonth());
            })
            // ── Filtros explícitos ──────────────────────────────────────────
            ->when($request->filled('barrio_id'),   fn($q) => $q->where('barrio_id', $request->barrio_id))
            ->when($request->filled('estado'),      fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('fecha_notificacion', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('fecha_notificacion', '<=', $request->fecha_hasta))
            ->latest('fecha_notificacion');

        return NotificacionResource::collection($query->paginate(20));
    }

    public function show($id)
    {
        $notificacion = Notificacion::with([
            'user',
            'barrio',
            'ordenanza332',
            'verificadoPorFuncionario.user',
            'aprobadoPorFuncionario.user',
            'rechazadoPorFuncionario.user',
            'verificadoPorSupervisor.user',
            'aprobadoPorSupervisor.user',
            'rechazadoPorSupervisor.user',
        ])->findOrFail($id);

        // El accessor `revisor` del modelo ya resuelve rol + usuario + fecha
        // según el estado actual, sin necesidad de mapear relaciones aquí.
        return new NotificacionResource($notificacion);
    }

    // ───────────────────────────────────────────────
    // BUSCAR POR NÚMERO DE DENUNCIA — cualquier usuario autenticado
    // Punto de entrada real: el propietario recibe el ID de la denuncia
    // por SMS/correo y lo usa para acceder a su notificación.
    // ───────────────────────────────────────────────
    public function buscarPorDenuncia(int $denunciaId)
    {
        $denuncia = Denuncia::find($denunciaId);

        if (!$denuncia) {
            return response()->json([
                'status'  => 404,
                'error'   => 'DENUNCIA_NO_ENCONTRADA',
                'message' => 'No existe una denuncia con ese número.',
            ], 404);
        }

        if ($denuncia->estado !== Denuncia::ESTADO_NOTIFICADA) {
            return response()->json([
                'status'  => 404,
                'error'   => 'SIN_NOTIFICACION_ACTIVA',
                'message' => 'Esta denuncia no tiene una notificación activa en este momento.',
            ], 404);
        }

        $notificacion = Notificacion::with(['barrio', 'ordenanza332', 'barrioAtributo'])
            ->where('denuncia_id', $denunciaId)
            ->firstOrFail();

        return new NotificacionResource($notificacion);
    }

    // ───────────────────────────────────────────────
    // PRESENTAR EVIDENCIA — cualquier usuario autenticado
    // ───────────────────────────────────────────────
    public function presentarEvidencia(StoreNotificacionEvidenciaRequest $request, Notificacion $notificacion, GeoService $geo)
    {
        $user = auth()->user();

        // ── 1. Reclamo previo por otro usuario ─────────────────────
        if ($notificacion->user_id && $notificacion->user_id !== $user->id) {
            return response()->json([
                'status'  => 409,
                'error'   => 'NOTIFICACION_RECLAMADA',
                'message' => 'Esta notificación ya fue reclamada por otro usuario.',
            ], 409);
        }

        // ── 2. Vencimiento del plazo ─────────────────────────────────
        if ($notificacion->fecha_vencimiento && now()->greaterThan($notificacion->fecha_vencimiento)) {
            if ($notificacion->estado !== Notificacion::ESTADO_VENCIDA) {
                $notificacion->update(['estado' => Notificacion::ESTADO_VENCIDA]);

                $denuncia = $notificacion->denuncia;
                $denuncia->update(['estado' => Denuncia::ESTADO_PENDIENTE]);

                $auditEvent = AuditEvent::logEvent(
                    $notificacion,
                    $user->id,
                    'notificacion_vencida',
                    ['denuncia_id' => $denuncia->id]
                );

                RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');
            }

            return response()->json([
                'status'  => 422,
                'error'   => 'PLAZO_VENCIDO',
                'message' => 'El plazo para presentar evidencia ha vencido.',
            ], 422);
        }

        // ── 3. Estado válido para recibir evidencia ──────────────────
        $estadosPermitidos = [Notificacion::ESTADO_PENDIENTE, Notificacion::ESTADO_ENVIADA];
        if (!in_array($notificacion->estado, $estadosPermitidos)) {
            return response()->json([
                'status'  => 422,
                'error'   => 'INVALID_STATE',
                'message' => 'Esta notificación no admite evidencia en su estado actual.',
            ], 422);
        }

        // ── 4. Intentos y cooldown, por sesión (usuario + notificación) ──
        $sessionKey = "notif_intentos.{$notificacion->id}.{$user->id}";
        $intento = session($sessionKey, ['count' => 0, 'last_at' => null]);

        if ($intento['count'] >= self::MAX_INTENTOS) {
            return response()->json([
                'status'  => 422,
                'error'   => 'MAX_INTENTOS_ALCANZADO',
                'message' => 'Alcanzaste el número máximo de intentos.',
            ], 422);
        }

        if ($intento['last_at'] && now()->diffInMinutes($intento['last_at']) < self::COOLDOWN_MINUTOS) {
            $espera = self::COOLDOWN_MINUTOS - now()->diffInMinutes($intento['last_at']);

            return response()->json([
                'status'  => 429,
                'error'   => 'COOLDOWN_ACTIVO',
                'message' => 'Debes esperar antes de intentar nuevamente.',
                'minutos_restantes' => $espera,
            ], 429);
        }

        $data = $request->validated();
        $denuncia = $notificacion->denuncia;

        // ── 5. Guardar archivo + verificar integridad ─────────────────
        $file = $request->file('evidencia');
        $path = $file->store('notificaciones', 'public');

        $hashMovil = $request->input('evidencia_hash');
        $hashServidor = hash_file('sha256', Storage::disk('public')->path($path));

        if (!hash_equals($hashMovil, $hashServidor)) {
            Storage::disk('public')->delete($path);

            Log::warning('Integridad de evidencia comprometida (notificación)', [
                'notificacion_id' => $notificacion->id,
                'user_id'         => $user->id,
                'hash_movil'      => $hashMovil,
                'hash_servidor'   => $hashServidor,
            ]);

            return response()->json([
                'status'  => 422,
                'error'   => 'EVIDENCE_INTEGRITY_MISMATCH',
                'message' => 'La evidencia recibida no coincide con la enviada. Intenta de nuevo.',
            ], 422);
        }

        // ── 6. Concurrencia geoespacial vs. la denuncia original ─────
        $porcentaje = $geo->calcularPorcentaje(
            (float) $data['latitud'],
            (float) $data['longitud'],
            (float) $denuncia->latitud,
            (float) $denuncia->longitud
        );

        if (!$geo->cumpleUmbral($porcentaje)) {
            Storage::disk('public')->delete($path);

            $intento['count']++;
            $intento['last_at'] = now();
            session([$sessionKey => $intento]);

            return response()->json([
                'status'  => 422,
                'error'   => 'GEO_MISMATCH',
                'message' => 'La evidencia no coincide con la ubicación de la denuncia. Acércate al lugar exacto para tomar la foto o video.',
                'intentos_restantes' => self::MAX_INTENTOS - $intento['count'],
            ], 422);
        }

        // ── 7. Éxito: guardar evidencia, asignar user_id y avanzar estado ──
        $notificacion->update([
            'user_id'        => $user->id,
            'evidencia_path' => $path,
            'evidencia_tipo' => str_contains($file->getMimeType(), 'video') ? 'video' : 'foto',
            'latitud'        => $data['latitud'],
            'longitud'       => $data['longitud'],
            'app_uuid'       => $data['app_uuid'] ?? null,
            'device_id'      => $data['device_id'],
            'os_version'     => $data['os_version'],
            'app_version'    => $data['app_version'],
            'estado'         => Notificacion::ESTADO_ENVIADA,
        ]);

        session()->forget($sessionKey);

        $auditEvent = AuditEvent::logEvent(
            $notificacion,
            $user->id,
            'notificacion_evidencia_presentada',
            [
                'porcentaje_concurrencia' => $porcentaje,
                'evidencia_tipo'          => $notificacion->evidencia_tipo,
                'latitud'                 => (string) $data['latitud'],
                'longitud'                => (string) $data['longitud'],
                'app_uuid'                => $data['app_uuid'] ?? null,
                'device_id'               => $data['device_id'],
                'os_version'              => $data['os_version'],
                'app_version'             => $data['app_version'],
            ]
        );

        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Evidencia presentada correctamente, pendiente de verificación.',
            'data'    => new NotificacionResource($notificacion->fresh()),
        ]);
    }

    // ───────────────────────────────────────────────
    // VERIFICAR — solo Funcionario
    // ───────────────────────────────────────────────
    public function verificar(Notificacion $notificacion)
    {
        $user = auth()->user();

        if ($user->role_name !== 'Funcionario') {
            return response()->json(['error' => 'Solo un funcionario puede verificar notificaciones.'], 403);
        }

        if ($notificacion->estado !== Notificacion::ESTADO_ENVIADA) {
            return response()->json(['error' => 'Solo se pueden verificar notificaciones con evidencia enviada.'], 422);
        }

        $funcionario = Funcionario::where('user_id', $user->id)->firstOrFail();

        $notificacion->update([
            'estado'             => Notificacion::ESTADO_VERIFICADA,
            'verificado_por_id'  => $funcionario->id,
            'verificado_por_rol' => 'Funcionario',
            'verificado_at'      => now(),
        ]);

        $auditEvent = AuditEvent::logEvent(
            $notificacion,
            $user->id,
            'notificacion_verificada',
            ['verificado_por' => $funcionario->id]
        );

        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Notificación verificada correctamente.',
            'data'    => new NotificacionResource($notificacion->fresh()),
        ]);
    }

    // ───────────────────────────────────────────────
    // APROBAR — solo Supervisor, cierra la Denuncia
    // ───────────────────────────────────────────────
    public function aprobar(Notificacion $notificacion)
    {
        $user = auth()->user();

        if ($user->role_name !== 'Supervisor') {
            return response()->json(['error' => 'Solo un supervisor puede aprobar notificaciones.'], 403);
        }

        if ($notificacion->estado !== Notificacion::ESTADO_VERIFICADA) {
            return response()->json(['error' => 'Solo se pueden aprobar notificaciones verificadas.'], 422);
        }

        $supervisor = Supervisor::where('user_id', $user->id)->firstOrFail();

        $notificacion->update([
            'estado'           => Notificacion::ESTADO_APROBADA,
            'aprobado_por_id'  => $supervisor->id,
            'aprobado_por_rol' => 'Supervisor',
            'aprobado_at'      => now(),
        ]);

        $denuncia = $notificacion->denuncia;
        $denuncia->update(['estado' => Denuncia::ESTADO_CERRADA]);

        $auditEvent = AuditEvent::logEvent(
            $notificacion,
            $user->id,
            'notificacion_aprobada',
            [
                'aprobado_por'    => $supervisor->id,
                'denuncia_id'     => $denuncia->id,
                'denuncia_estado' => Denuncia::ESTADO_CERRADA,
            ]
        );

        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Notificación aprobada. Justificación aceptada, denuncia cerrada.',
            'data'    => [
                'notificacion' => new NotificacionResource($notificacion->fresh()),
                'denuncia'     => $denuncia->fresh(),
            ],
        ]);
    }

    // ───────────────────────────────────────────────
    // RECHAZAR — Funcionario o Supervisor, reabre la Denuncia
    // ───────────────────────────────────────────────
    public function rechazar(Request $request, Notificacion $notificacion)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|min:10|max:500',
        ]);

        $user = auth()->user();

        if (!in_array($user->role_name, ['Funcionario', 'Supervisor'])) {
            return response()->json(['error' => 'No tienes permiso para rechazar notificaciones.'], 403);
        }

        $estadosPermitidos = [Notificacion::ESTADO_ENVIADA, Notificacion::ESTADO_VERIFICADA];
        if (!in_array($notificacion->estado, $estadosPermitidos)) {
            return response()->json(['error' => 'Esta notificación no puede ser rechazada en su estado actual.'], 422);
        }

        $notificacion->update([
            'estado'            => Notificacion::ESTADO_RECHAZADA,
            'rechazado_por_id'  => $user->id,
            'rechazado_por_rol' => $user->role_name,
            'rechazado_at'      => now(),
            'motivo_rechazo'    => $request->motivo_rechazo,
        ]);

        $denuncia = $notificacion->denuncia;
        $denuncia->update(['estado' => Denuncia::ESTADO_PENDIENTE]);

        $auditEvent = AuditEvent::logEvent(
            $notificacion,
            $user->id,
            'notificacion_rechazada',
            [
                'motivo'          => $request->motivo_rechazo,
                'denuncia_id'     => $denuncia->id,
                'denuncia_estado' => Denuncia::ESTADO_PENDIENTE,
            ]
        );

        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Notificación rechazada. La denuncia vuelve a estado pendiente.',
            'data'    => [
                'notificacion' => new NotificacionResource($notificacion->fresh()),
                'denuncia'     => $denuncia->fresh(),
            ],
        ]);
    }
}
