<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Importa tus clases de excepción
use App\Exceptions\DatabaseException;
use App\Exceptions\MenuServiceException;

// Agregar dentro de ->withExceptions(function (Exceptions $exceptions) { ... })
// en bootstrap/app.php:

use App\Exceptions\Documento\DocumentoAccesoDenegadoException;
use App\Exceptions\Documento\DocumentoInvalidoException;
use App\Exceptions\Documento\DocumentoNoEncontradoException;
use App\Services\LogSistemaService;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',

        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('operacion')
                ->group(base_path('routes/operacion.php'));

            Route::middleware('web')
                ->prefix('dashboard')
                ->group(base_path('routes/dashboard.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 1. Manejo para errores de Base de Datos (Conexión, Auth, PDO)
        $exceptions->render(function (DatabaseException $e, Request $request) {
            return response()->json([
                'status'  => 500,
                'error'   => 'DB_INFRASTRUCTURE_ERROR',
                'message' => 'El servicio de datos no está disponible temporalmente.',
                'debug'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        });

        // 2. Manejo para errores de Lógica de Menús
        $exceptions->render(function (MenuServiceException $e, Request $request) {
            return response()->json([
                'status'  => 422,
                'error'   => 'MENU_LOGIC_ERROR',
                'message' => $e->getMessage(), // Ej: "El ID del rol debe ser mayor a 0"
            ], 422);
        });

        // 3. Manejo genérico para QueryException (por si algo escapa al try/catch)
        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status'  => 500,
                    'error'   => 'QUERY_ERROR',
                    'message' => 'Error inesperado al consultar la base de datos.',
                    'debug'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }
        });

        $exceptions->render(function (DocumentoNoEncontradoException $e, $request) {
            LogSistemaService::registrar(
                origen: 'DocumentoController',
                tipoOrigen: 'ROUTE',
                nivel: 'WARNING', // probablemente dato viejo, no un ataque
                comentario: 'Documento no encontrado en disco',
                mensajeError: ['ruta_esperada' => $e->rutaEsperada, 'usuario' => auth()->id()],
            );

            return response()->view('errors.documento', [
                'titulo'      => 'Documento no disponible',
                'mensaje'     => 'El archivo asociado a este registro ya no existe en el servidor. '
                    . 'Es posible que sea un registro antiguo de antes de una reorganización '
                    . 'de archivos, o que el documento haya sido movido o eliminado.',
                'detalles'    => app()->environment('local') ? ['ruta_esperada' => $e->rutaEsperada] : [],
                'rutaRegreso' => url()->previous(),
                'textoBoton'  => 'Cerrar pestaña',
            ], 404);
        });

        $exceptions->render(function (DocumentoAccesoDenegadoException $e, $request) {
            LogSistemaService::registrar(
                origen: 'DocumentoController',
                tipoOrigen: 'ROUTE',
                nivel: 'ERROR', // path traversal real es señal de manipulación deliberada
                comentario: 'Intento de acceso fuera del directorio permitido',
                mensajeError: ['usuario' => auth()->id(), 'ruta' => $request->path()],
            );

            return response()->view('errors.documento', [
                'titulo'      => 'Acceso no permitido',
                'mensaje'     => 'No tienes permiso para acceder a este documento.',
                'detalles'    => [],
                'rutaRegreso' => url()->previous(),
                'textoBoton'  => 'Cerrar pestaña',
            ], 403);
        });

        $exceptions->render(function (DocumentoInvalidoException $e, $request) {
            return response()->view('errors.documento', [
                'titulo'      => 'Solicitud inválida',
                'mensaje'     => $e->getMessage(),
                'detalles'    => [],
                'rutaRegreso' => url()->previous(),
                'textoBoton'  => 'Cerrar pestaña',
            ], 400);
        });
    })->create();
