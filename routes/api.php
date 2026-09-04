<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\{Route, Auth};

use App\Models\{Contrato, AuditEvent};

use App\Http\Controllers\{AuthController, CatalogoController, DenunciaController, EvidenceController, MetricaController, VecinoController, BarrioController, MultaController, NotificacionController};

use App\Http\Controllers\Api\{ContravencionController, ContratoServicioContratistaController, HitoSyncController, MediaUploadController, EvidenciaSyncController};


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
Route::get('/barrios', [BarrioController::class, 'apiIndex']);

// Ruta agrupados con nombre
Route::get('/catalogos/agrupados', [CatalogoController::class, 'agrupados'])
    ->name('catalogos.agrupados');

// Ruta principal con nombre
Route::get('/catalogos', [CatalogoController::class, 'index'])
    ->name('catalogos.index');

// Ruta de sincronización con el nombre que está buscando tu sistema
Route::get('/catalogos', [CatalogoController::class, 'sync'])
    ->name('catalogos.sync');


/*
|--------------------------------------------------------------------------
| Rutas de hitos y sync desde app móvil
|--------------------------------------------------------------------------
| Agregar dentro del grupo con middleware auth:sanctum (o el que uses
| para autenticar la app móvil) en routes/api.php
*/

Route::middleware('auth:sanctum')->group(function () {

    // Contratista: navegación contrato -> servicios antes de capturar hitos
    Route::get('mis-contratos-servicios', [ContratoServicioContratistaController::class, 'index']);
    Route::get('contratos-servicios/{contrato}/detalles', [ContratoServicioContratistaController::class, 'detalles']);

    // Paso 1: subir archivo binario (idempotente por uuid)
    Route::post('media-uploads', [MediaUploadController::class, 'store']);

    // Paso 2: sync batch de metadatos de hitos + evidencias
    Route::post('sync/hitos', [HitoSyncController::class, 'sync']);

    Route::post('/sync/evidencias', [EvidenciaSyncController::class, 'sync']);
    // Acciones de Dirigente / Presidente (panel o app móvil)
    Route::post('hitos/{hito}/verificar', [HitoSyncController::class, 'verificar']);
    Route::post('hitos/{hito}/aprobar', [HitoSyncController::class, 'aprobar']);
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
        Route::get('/',                        [DenunciaController::class, 'index']);
        Route::post('/',                       [DenunciaController::class, 'store']);
        Route::get('/{id}',                    [DenunciaController::class, 'show']);
        Route::patch('/{denuncia}/verificar',  [DenunciaController::class, 'verificar']);   // Funcionario
        Route::patch('/{denuncia}/aprobar',    [DenunciaController::class, 'aprobar']);     // Supervisor
        Route::patch('/{denuncia}/rechazar',   [DenunciaController::class, 'rechazar']);    // Funcionario o Supervisor
    });
    // 🔔 Notificaciones
    Route::prefix('notificaciones')->group(function () {
        Route::get('/',                           [NotificacionController::class, 'index']);
        Route::get('/{notificacion}',             [NotificacionController::class, 'show']);
        Route::get('/buscar/{denunciaId}',        [NotificacionController::class, 'buscarPorDenuncia']);
        Route::post('/{notificacion}/evidencia',  [NotificacionController::class, 'presentarEvidencia']); // cualquier usuario autenticado
        Route::patch('/{notificacion}/verificar', [NotificacionController::class, 'verificar']);  // Funcionario
        Route::patch('/{notificacion}/aprobar',   [NotificacionController::class, 'aprobar']);    // Supervisor
        Route::patch('/{notificacion}/rechazar',  [NotificacionController::class, 'rechazar']);   // Funcionario o Supervisor
    });
    // 🖼️ Evidencias
    Route::prefix('evidences')->group(function () {
        Route::post('/',                [EvidenceController::class, 'store']);
        Route::post('/evidence/upload', [EvidenceController::class, 'store']);
    });



    // 💰 Multas
    Route::prefix('multas')->group(function () {
        Route::get('/',               [MultaController::class, 'index']);
        Route::get('/{multa}',        [MultaController::class, 'show']);
        Route::post('/{multa}/pagar', [MultaController::class, 'pagar']);
    });

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
