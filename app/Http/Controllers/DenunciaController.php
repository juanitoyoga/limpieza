<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\Vecino;
use App\Models\Barrio;
use App\Models\Ordenanza332;
use App\Models\AuditEvent;
use App\Models\Funcionario;
use App\Models\Supervisor;
use App\Models\Contrato;
use App\Models\SalarioMinimo;
use App\Models\Multa;
use App\Http\Requests\StoreDenunciaRequest;
use App\Http\Resources\DenunciaResource;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class DenunciaController extends Controller
{


    public function index(Request $request)
    {
        $user     = auth()->user();
        $esVecino = $user->role_name === 'Vecino';

        $tieneFiltros = $request->hasAny(['estado', 'barrio_id', 'fecha_desde', 'fecha_hasta']);

        $query = Denuncia::with(['vecino.user', 'barrio', 'ordenanza332']) // Cargamos relaciones core válidas
            // ── Fallback: vecino sin filtros → su barrio + último mes ──────
            ->when($esVecino && !$tieneFiltros, function ($q) use ($user) {
                $vecino = \App\Models\Vecino::where('user_id', $user->id)->firstOrFail();
                $q->where('barrio_id', $vecino->barrio_id)
                    ->where('fecha_denuncia', '>=', now()->subMonth());
            })
            // ── Filtros explícitos ──────────────────────────────────────────
            ->when($request->filled('barrio_id'),   fn($q) => $q->where('barrio_id', $request->barrio_id))
            ->when($request->filled('estado'),      fn($q) => $q->where('estado', $request->estado))
            ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('fecha_denuncia', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('fecha_denuncia', '<=', $request->fecha_hasta))
            ->latest('fecha_denuncia');

        return DenunciaResource::collection($query->paginate(20));
    }

    public function show($id)
    {
        $denuncia = Denuncia::with(['vecino.user', 'barrio', 'ordenanza332'])->findOrFail($id);

        // Determinamos dinámicamente qué relación cargar según el rol y el estado actual de la denuncia
        $mapaRelaciones = [
            'Dirigente'   => ['Verificado' => 'verificadoPorDirigente.user', 'Aprobada' => 'aprobadoPorDirigente.user', 'Rechazada' => 'rechazadoPorDirigente.user'],
            'Funcionario' => ['Verificado' => 'verificadoPorFuncionario.user', 'Aprobada' => 'aprobadoPorFuncionario.user', 'Rechazada' => 'rechazadoPorFuncionario.user'],
            'Supervisor'  => ['Verificado' => 'verificadoPorSupervisor.user', 'Aprobada' => 'aprobadoPorSupervisor.user', 'Rechazada' => 'rechazadoPorSupervisor.user'],
        ];

        // Mapeamos el estado al flujo correspondiente del accessor
        $estadoFlujo = match ($denuncia->estado) {
            Denuncia::ESTADO_VERIFICADA => 'Verificada',
            Denuncia::ESTADO_APROBADA   => 'Aprobada',
            Denuncia::ESTADO_RECHAZADA  => 'Rechazada',
            default                     => null,
        };

        // Si hay un rol y un estado válido en el flujo, cargamos el usuario exacto mediante Eloquent
        $rolResponsable = $denuncia->{"{$denuncia->estado}_por_rol"} ?? null;

        if ($estadoFlujo && $rolResponsable && isset($mapaRelaciones[$rolResponsable][$estadoFlujo])) {
            $denuncia->load($mapaRelaciones[$rolResponsable][$estadoFlujo]);
        }

        return new DenunciaResource($denuncia);
    }

    // ───────────────────────────────────────────────
    // VERIFICAR — solo Funcionario
    // ───────────────────────────────────────────────
    public function verificar(Denuncia $denuncia)
    {
        $user = auth()->user();

        if ($user->role_name !== 'Funcionario') {
            return response()->json(['error' => 'Solo un funcionario puede verificar denuncias.'], 403);
        }

        if ($denuncia->estado !== Denuncia::ESTADO_PENDIENTE) {
            return response()->json(['error' => 'Solo se pueden verificar denuncias pendientes.'], 422);
        }

        $funcionario = Funcionario::where('user_id', $user->id)->firstOrFail();

        $denuncia->update([
            'estado'             => Denuncia::ESTADO_VERIFICADA,
            'verificado_por_id'  => $funcionario->id,
            'verificado_por_rol' => 'Funcionario',
            'verificado_at'      => now(),
        ]);

        $auditEvent = AuditEvent::logEvent(
            $denuncia,
            $user->id,
            'denuncia_verificada',
            ['verificado_por' => $funcionario->id]
        );

        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Denuncia verificada correctamente.',
            'data'    => $denuncia->fresh(),
        ]);
    }

    // ───────────────────────────────────────────────
    // APROBAR — solo Supervisor, genera multa
    // ───────────────────────────────────────────────
    public function aprobar(Denuncia $denuncia)
    {
        $user = auth()->user();

        if ($user->role_name !== 'Supervisor') {
            return response()->json([
                'status'  => 403,
                'error'   => 'FORBIDDEN',
                'message' => 'Solo un supervisor puede aprobar denuncias.',
            ], 403);
        }

        if ($denuncia->estado !== Denuncia::ESTADO_VERIFICADA) {
            return response()->json([
                'status'  => 422,
                'error'   => 'INVALID_STATE',
                'message' => 'Solo se pueden aprobar denuncias verificadas.',
            ], 422);
        }

        $supervisor = Supervisor::where('user_id', $user->id)->firstOrFail();

        // ── Calcular multa ────────────────────────────────────────
        $ordenanza  = Ordenanza332::findOrFail($denuncia->ordenanza332_id);
        $porcentaje = $ordenanza->porcentajeVigente();
        $salario    = SalarioMinimo::vigente();

        if (!$porcentaje || !$salario) {
            return response()->json([
                'status'  => 422,
                'error'   => 'MISSING_SALARY_CONFIG',
                'message' => 'No hay salario mínimo o porcentaje vigente configurado.',
            ], 422);
        }

        $valorMulta = round(($porcentaje->porcentaje / 100) * $salario->valor_usd, 2);

        // ── Distribución desde contrato vigente del barrio ────────
        $contrato = Contrato::where('barrio_id', $denuncia->barrio_id)
            ->where('estado', Contrato::ESTADO_APROBADO)
            ->latest()
            ->first();

        if (!$contrato) {
            return response()->json([
                'status'  => 422,
                'error'   => 'NO_CONTRACT',
                'message' => 'El barrio no tiene un contrato aprobado vigente.',
            ], 422);
        }

        $valorBarrio     = round($valorMulta * ($contrato->porcentaje_barrio / 100), 2);
        $valorMunicipio  = round($valorMulta * ($contrato->porcentaje_dmq    / 100), 2);
        $valorPlataforma = round($valorMulta * ($contrato->porcentaje_ltr    / 100), 2);

        // ── Actualizar denuncia ───────────────────────────────────
        $denuncia->update([
            'estado'           => Denuncia::ESTADO_APROBADA,
            'aprobado_por_id'  => $supervisor->id,
            'aprobado_por_rol' => 'Supervisor',
            'aprobado_at'      => now(),
            'multa_calculada'  => $valorMulta,
        ]);

        // ── Crear multa ───────────────────────────────────────────
        $multa = Multa::create([
            'denuncia_id'           => $denuncia->id,
            'ordenanza332_id'       => $denuncia->ordenanza332_id,
            'vecino_id'             => $denuncia->vecino_id,
            'supervisor_id'         => $supervisor->id,
            'barrio_id'             => $denuncia->barrio_id,
            'codigo_unico'          => 'MLT-' . strtoupper(Str::random(8)),
            'porcentaje_salario'    => $porcentaje->porcentaje,
            'salario_base'          => $salario->valor_usd,
            'valor_multa'           => $valorMulta,
            'porcentaje_barrio'     => $contrato->porcentaje_barrio,
            'valor_barrio'          => $valorBarrio,
            'porcentaje_municipio'  => $contrato->porcentaje_dmq,
            'valor_municipio'       => $valorMunicipio,
            'porcentaje_plataforma' => $contrato->porcentaje_ltr,
            'valor_plataforma'      => $valorPlataforma,
            'estado'                => 'Pendiente',
            'fecha_emision'         => now(),
            'fecha_vencimiento'     => now()->addDays(30),
        ]);

        // ── Auditoría denuncia aprobada ───────────────────────────
        $auditDenuncia = AuditEvent::logEvent(
            $denuncia,
            $user->id,
            'denuncia_aprobada',
            [
                'aprobado_por' => $supervisor->id,
                'multa_id'     => $multa->id,
                'valor_multa'  => $valorMulta,
            ]
        );

        RegistrarEventoBlockchain::dispatch($auditDenuncia->id)->onQueue('blockchain');

        // ── Auditoría multa emitida ───────────────────────────────
        $auditMulta = AuditEvent::logEvent(
            $multa,
            $user->id,
            'multa_emitida',
            [
                'valor_multa'     => $valorMulta,
                'valor_barrio'    => $valorBarrio,
                'valor_municipio' => $valorMunicipio,
                'valor_plataforma' => $valorPlataforma,
            ]
        );

        RegistrarEventoBlockchain::dispatch($auditMulta->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Denuncia aprobada y multa emitida.',
            'data'    => [
                'denuncia' => $denuncia->fresh(),
                'multa'    => $multa,
            ],
        ]);
    }
    // ───────────────────────────────────────────────
    // RECHAZAR — Funcionario o Supervisor
    // ───────────────────────────────────────────────
    public function rechazar(Request $request, Denuncia $denuncia)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|min:10|max:500',
        ]);

        $user = auth()->user();

        $rolesPermitidos = ['Funcionario', 'Supervisor'];
        if (!in_array($user->role_name, $rolesPermitidos)) {
            return response()->json(['error' => 'No tienes permiso para rechazar denuncias.'], 403);
        }

        $estadosPermitidos = [Denuncia::ESTADO_PENDIENTE, Denuncia::ESTADO_VERIFICADA];
        if (!in_array($denuncia->estado, $estadosPermitidos)) {
            return response()->json(['error' => 'Esta denuncia no puede ser rechazada en su estado actual.'], 422);
        }

        $denuncia->update([
            'estado'             => Denuncia::ESTADO_RECHAZADA,
            'rechazado_por_id'   => $user->id,
            'rechazado_por_rol'  => $user->role_name,
            'rechazado_at'       => now(),
            'motivo_rechazo'     => $request->motivo_rechazo,
        ]);

        $auditEvent = AuditEvent::logEvent(
            $denuncia,
            $user->id,
            'denuncia_rechazada',
            ['motivo' => $request->motivo_rechazo]
        );

        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        return response()->json([
            'status'  => 200,
            'message' => 'Denuncia rechazada.',
            'data'    => $denuncia->fresh(),
        ]);
    }

    public function store(StoreDenunciaRequest $request)
    {
        $data = $request->validated();
        $userId = auth()->id(); // ← obtener ID del usuario

        // ───────────────────────────────────────────────
        // 1. Validaciones cruzadas de seguridad
        // ───────────────────────────────────────────────

        $vecino = Vecino::findOrFail($data['vecino_id']);
        $barrio = Barrio::findOrFail($request->input('barrio_id'));

        if ($vecino->id_DMQ !== $barrio->id_DMQ) {
            return response()->json([
                'status'  => 422,
                'error'   => 'INVALID_BARRIO',
                'message' => 'El vecino no pertenece al barrio indicado.',
            ], 422);
        }

        if (!Ordenanza332::find($data['ordenanza332_id'])) {
            return response()->json([
                'status'  => 422,
                'error'   => 'INVALID_ORDENANZA',
                'message' => 'La contravención no existe.'
            ], 422);
        }

        // ───────────────────────────────────────────────
        // 2. Reverse Geocoding (lat/lng → dirección)
        // ───────────────────────────────────────────────

        if ($request->latitud && $request->longitud) {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$request->latitud}&lon={$request->longitud}";
            $geo = Http::get($url)->json();
            $data['direccion_gps'] = $geo['display_name'] ?? null;
        }

        // ───────────────────────────────────────────────
        // 3. Guardar evidencia (foto/video)
        // ───────────────────────────────────────────────

        if ($request->hasFile('evidencia')) {
            $file = $request->file('evidencia');
            $path = $file->store('denuncias', 'public');

            $data['evidencia_path'] = $path;
            $data['evidencia_tipo'] = Str::contains($file->getMimeType(), 'video')
                ? 'video'
                : 'foto';
        }
        $hashMovil = $request->input('evidencia_hash');
        $hashServidor = hash_file('sha256', Storage::disk('public')->path($data['evidencia_path']));

        if (!hash_equals($hashMovil, $hashServidor)) {

            // Borrar el archivo sospechoso — no debe quedar en storage
            Storage::disk('public')->delete($data['evidencia_path']);

            Log::warning('Integridad de evidencia comprometida', [
                'vecino_id'     => $data['vecino_id'],
                'hash_movil'    => $hashMovil,
                'hash_servidor' => $hashServidor,
            ]);

            return response()->json([
                'status'  => 422,
                'error'   => 'EVIDENCE_INTEGRITY_MISMATCH',
                'message' => 'La evidencia recibida no coincide con la enviada. Intenta de nuevo.',
            ], 422);
        }

        // ───────────────────────────────────────────────
        // 4. Fecha de denuncia
        // ───────────────────────────────────────────────

        $data['fecha_denuncia'] = now();

        // ───────────────────────────────────────────────
        // 5. Crear denuncia en base de datos
        // ───────────────────────────────────────────────

        $denuncia = Denuncia::create($data);

        // ───────────────────────────────────────────────
        // 6. Registrar evento de auditoría
        // ───────────────────────────────────────────────

        $auditEvent = AuditEvent::logEvent(
            $denuncia,
            $userId,
            'denuncia_pendiente',
            [
                'evidencia_tipo'  => $denuncia->evidencia_tipo,
                'evidencia_hash'  => Storage::disk('public')->exists($denuncia->evidencia_path)
                    ? hash_file('sha256', Storage::disk('public')->path($denuncia->evidencia_path))
                    : null,
                'latitud'         => (string) $denuncia->latitud,
                'longitud'        => (string) $denuncia->longitud,
                'barrio_id'       => $denuncia->barrio_id,
                'ordenanza332_id' => $denuncia->ordenanza332_id,
            ]
        );

        $denuncia->update([
            'file_hash'         => $auditEvent->event_hash,
            'blockchain_status' => 'pendiente',
        ]);

        // ───────────────────────────────────────────────
        // 7. Despachar a blockchain (async)
        // ───────────────────────────────────────────────

        RegistrarEventoBlockchain::dispatch($auditEvent->id)
            ->onQueue('blockchain');

        // ───────────────────────────────────────────────
        // 8. Respuesta final — INMEDIATA, no espera blockchain
        // ───────────────────────────────────────────────

        return response()->json([
            'status'  => 200,
            'message' => 'Denuncia registrada correctamente. Verificación blockchain en proceso.',
            'data'    => $denuncia,
        ]);
    }
}
