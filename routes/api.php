<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Contrato;
use App\Models\AuditEvent;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\DenunciaController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\MetricaController;
use App\Http\Controllers\VecinoController;
use App\Http\Controllers\BarrioController;
use App\Http\Controllers\Api\ContravencionController;

/*
|--------------------------------------------------------------------------
| API Routes - LimpiaTuRincon
|--------------------------------------------------------------------------
|
| Prefijo base: /api  (definido en bootstrap/app.php o RouteServiceProvider)
|
| IMPORTANTE: Las rutas de auth de la app móvil usan el prefijo /auth/
| para no colisionar con el POST /login del login web (Fortify/Jetstream).
|
| App móvil usa:  POST /api/auth/login   y   POST /api/auth/register
| App web usa:    POST /login            (manejado por Fortify en web.php)
|
*/

// --- RUTAS PÚBLICAS APP MÓVIL ---
Route::prefix('auth')->group(function () {
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// --- RUTAS PÚBLICAS GENERALES ---
Route::get('/barrios', [BarrioController::class, 'index']);

// 📋 Catálogos — públicos para que la app los cargue sin autenticación
Route::prefix('catalogos')->group(function () {
    Route::get('/',          [CatalogoController::class, 'index']);
    Route::get('/agrupados', [CatalogoController::class, 'agrupados']);
});

// --- RUTAS PROTEGIDAS (Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    // 👤 Perfil de Usuario
    Route::get('/user', fn(Request $request) => $request->user());

    // 🏘️ Vecinos
    Route::prefix('vecinos')->group(function () {
        Route::get('/me',                 [VecinoController::class, 'me']);
        Route::post('/',                  [VecinoController::class, 'store']);
        Route::post('/validar-ubicacion', [VecinoController::class, 'validarUbicacion']);
    });
    // 📋 Catálogo de contravenciones
    Route::get('/contravenciones', [ContravencionController::class, 'index']);
    // 📝 Denuncias
    Route::prefix('denuncias')->group(function () {
        Route::get('/',     [DenunciaController::class, 'index']);
        Route::post('/',    [DenunciaController::class, 'store']);
        Route::get('/{id}', [DenunciaController::class, 'show']);
    });

    // 🖼️ Evidencias
    Route::prefix('evidences')->group(function () {
        Route::post('/',                [EvidenceController::class, 'store']);
        Route::post('/evidence/upload', [EvidenceController::class, 'store']);
    });

    // 🔁 Sync Wikidata — solo administradores
    Route::post('/catalogos/sync', [CatalogoController::class, 'sync']);

    // 📊 Métricas
    Route::get('/metricas', [MetricaController::class, 'getStats']);

    // ⛓️ Blockchain
    Route::post('/contratos/{contrato}/blockchain', function (Request $request, Contrato $contrato) {
        $validated = $request->validate([
            'wallet_address' => 'required|string|max:255',
            'tx_hash'        => 'required|string|max:255',
            'network'        => 'required|string|max:50',
            'document_hash'  => 'required|string|max:255',
        ]);

        $contrato->registrarBlockchain(
            $validated['tx_hash'],
            $validated['network'],
            $validated['document_hash']
        );

        AuditEvent::logEvent(
            $contrato,
            Auth::user()->id,
            AuditEvent::EVENT_BLOCKCHAIN_REGISTERED,
            [
                'tx_hash' => $validated['tx_hash'],
                'wallet'  => $validated['wallet_address'],
                'network' => $validated['network'],
            ]
        );

        return response()->json([
            'ok'          => true,
            'contrato_id' => $contrato->id,
            'tx_hash'     => $validated['tx_hash'],
        ]);
    });
});
