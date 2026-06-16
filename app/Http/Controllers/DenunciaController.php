<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\Vecino;
use App\Models\Ordenanza332;
use App\Models\AuditEvent;
use App\Http\Requests\StoreDenunciaRequest;
use App\Http\Resources\DenunciaResource;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class DenunciaController extends Controller
{


    public function index()
    {
        return DenunciaResource::collection(
            Denuncia::with(['vecino', 'ordenanza332', 'dirigente', 'funcionario'])
                ->latest()
                ->paginate(20)
        );
    }

    public function show($id)
    {
        return new DenunciaResource(
            Denuncia::with(['vecino', 'ordenanza332', 'dirigente', 'funcionario'])
                ->findOrFail($id)
        );
    }


public function store(StoreDenunciaRequest $request, BlockchainService $blockchain)
{
    $data = $request->validated();
    $userId = auth()->id(); // ← obtener ID del usuario

    // ───────────────────────────────────────────────
    // 1. Validaciones cruzadas de seguridad
    // ───────────────────────────────────────────────

    $vecino = Vecino::findOrFail($data['vecino_id']);

    if ($vecino->barrio_id != $request->input('barrio_id')) {
        return response()->json([
            'status'  => 422,
            'error'   => 'INVALID_BARRIO',
            'message' => 'El vecino no pertenece al barrio indicado.'
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

    // ───────────────────────────────────────────────
    // 4. Fecha de denuncia
    // ───────────────────────────────────────────────

    $data['fecha_denuncia'] = now();

    // ───────────────────────────────────────────────
    // 5. Crear denuncia en base de datos
    // ───────────────────────────────────────────────

    $denuncia = Denuncia::create($data);

    // Registrar evento de creación con logEvent (correcto)
    AuditEvent::logEvent(
        $denuncia,
        $userId,
        AuditEvent::EVENT_NOMINATION_CREATED, // o crea un EVENT_DENUNCIA_CREATED si quieres
        $data
    );

    // ───────────────────────────────────────────────
    // 6. Generar hash SHA‑256 de la denuncia
    // ───────────────────────────────────────────────

    $raw = implode('|', [
        $denuncia->id,
        $denuncia->vecino_id,
        $denuncia->ordenanza332_id,
        $denuncia->latitud,
        $denuncia->longitud,
        $denuncia->direccion_gps,
        $denuncia->descripcion,
        $denuncia->device_id,
        $denuncia->os_version,
        $denuncia->app_version,
        $denuncia->evidencia_path,
        $denuncia->fecha_denuncia,
    ]);

    $hash = hash('sha256', $raw);
    $denuncia->update(['file_hash' => $hash]);

    // ───────────────────────────────────────────────
    // 7. Registrar en Blockchain
    // ───────────────────────────────────────────────

    try {
        $txHash = $blockchain->registrarDenunciaBlockchain($denuncia->id, $hash, $userId);

        $denuncia->update([
            'tx_hash'           => $txHash,
            'blockchain_status' => 'confirmed',
            'verified_on_chain' => true,
        ]);

        // Evento de registro en blockchain con logEvent
        AuditEvent::logEvent(
            $denuncia,
            $userId,
            AuditEvent::EVENT_BLOCKCHAIN_REGISTERED,
            ['hash' => $hash, 'tx_hash' => $txHash]
        );

        // O si quieres actualizar el evento anterior con blockchain_hash:
        // $denuncia->auditEvents()->latest()->first()->recordBlockchainTransaction($hash, $txHash);
    } catch (Exception $e) {

        $denuncia->update([
            'blockchain_status' => 'failed',
            'verified_on_chain' => false,
        ]);

        // Evento de fallo en blockchain con logEvent
        AuditEvent::logEvent(
            $denuncia,
            $userId,
            'BLOCKCHAIN_FAILED',
            ['error' => $e->getMessage()]
        );
    }

    // ───────────────────────────────────────────────
    // 8. Respuesta final
    // ───────────────────────────────────────────────

    return response()->json([
        'status'  => 200,
        'message' => 'Denuncia registrada correctamente',
        'data'    => $denuncia,
    ]);
}
}
