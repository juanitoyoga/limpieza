<?php

use App\Models\Nomination;

use App\Livewire\Public\Welcome;

use Illuminate\Support\Facades\{Auth, Route, Storage, Response};


use App\Livewire\Admin\Home as AdminHome;


use App\Livewire\Dashboard\Home as DashboardHome;

use App\Livewire\Operacion\Home as OperacionHome;

use App\Livewire\Operacion\Simple as OperacionSimple;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('general.info');
    }
    return view('welcome');
});



Route::get('/descargar-documento/{path}', function ($path) {
    return Storage::download($path);
})->where('path', '.*')->name('descargar-documento');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/info', Welcome::class)->name('general.info');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/operacion/home', OperacionHome::class)->name('operacion.home');
    Route::get('/operacion/simple', OperacionSimple::class)->name('operacion.simple');
    Route::get('/dashboard/home', DashboardHome::class)->name('dashboard.home');
    Route::get('/admin/home', AdminHome::class)->middleware('can:admin-access')->name('admin.home');
});


Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/ver-documento/{disco}/{path}', function ($disco, $path) {
    // Whitelist de discos permitidos — evita que alguien pida un disco arbitrario
    $discosPermitidos = ['nominations', 'resoluciones'];

    if (!in_array($disco, $discosPermitidos, true)) {
        abort(404, 'Disco no válido.');
    }

    $decodedPath = base64_decode($path, true);

    if ($decodedPath === false) {
        abort(404, 'Ruta inválida.');
    }

    $fullPath = storage_path("app/{$disco}/{$decodedPath}");
    $basePath = storage_path("app/{$disco}");

    // Verificación de seguridad: el path resuelto debe seguir dentro del directorio del disco
    if (!str_starts_with(realpath($fullPath) ?: '', realpath($basePath))) {
        abort(403, 'Acceso no permitido.');
    }

    if (!file_exists($fullPath)) {
        abort(404, 'El archivo físico no existe.');
    }

    return Response::file($fullPath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="documento.pdf"',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
})->name('ver.documento')->middleware('auth');
