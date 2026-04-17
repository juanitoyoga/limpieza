<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Contrato;
use App\Models\AuditEvent;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DenunciaController;
use App\Http\Controllers\Api\MetricaController;

/*
|--------------------------------------------------------------------------
| API Routes - LimpiaTuRincon
|--------------------------------------------------------------------------
*/

// --- RUTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


// --- RUTAS PROTEGIDAS (Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    // 👤 Perfil de Usuario/Vecino
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 📝 Denuncias y Reportes de Vecinos
    Route::prefix('denuncias')->group(function () {
        Route::get('/', [DenunciaController::class, 'index']);      // Ver mis reportes
        Route::post('/', [DenunciaController::class, 'store']);     // Crear reporte
        Route::get('/{id}', [DenunciaController::class, 'show']);   // Detalle
    });

    // 📊 Métricas y Consultas
    Route::get('/metricas', [MetricaController::class, 'getStats']);

    // ⛓️ RUTAS DE BLOCKCHAIN (Tus rutas actuales merged ✅)
    Route::post('/contratos/{contrato}/blockchain', function (Request $request, Contrato $contrato) {

        $validated = $request->validate([
            'wallet_address' => 'required|string|max:255',
            'tx_hash'        => 'required|string|max:255',
            'network'        => 'required|string|max:50',
            'document_hash'  => 'required|string|max:255',
        ]);

        // 🧾 Actualizar contrato
        $contrato->registrarBlockchain(
            $validated['tx_hash'],
            $validated['network'],
            $validated['document_hash']
        );

        // 🧾 Auditoría (POLIMÓRFICA ✅)
        AuditEvent::logEvent(
            $contrato,                // ✅ MODELO
            auth()->id(),
            AuditEvent::EVENT_BLOCKCHAIN_REGISTERED,
            [
                'tx_hash' => $validated['tx_hash'],
                'wallet'  => $validated['wallet_address'],
                'network' => $validated['network'],
            ]
        );

        return response()->json([
            'ok' => true,
            'contrato_id' => $contrato->id,
            'tx_hash' => $validated['tx_hash'],
        ]);
    });

});