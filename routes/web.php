<?php

use App\Models\Nomination;

use App\Livewire\Public\Welcome;

use Illuminate\Support\Facades\{Auth, Route, Storage, Response};


use App\Livewire\Admin\Home as AdminHome;


use App\Livewire\Dashboard\Home as DashboardHome;

use App\Livewire\Operacion\Home as OperacionHome;

use App\Livewire\Operacion\Simple as OperacionSimple;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DocumentoController;


// Agregar en routes/web.php:

if (app()->environment('local')) {
    require __DIR__ . '/mail-preview.php';
}

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('general.info');
    }
    return view('welcome');
});

Route::get('/usuarios/{user}/sesiones', function (\App\Models\User $user) {
    return view('admin.sesiones', ['userId' => $user->id]);
})->name('usuarios.sesiones')->middleware('can:gestionar-sesiones');

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

Route::get('/ver-documento/{disco}/{path}', [DocumentoController::class, 'ver'])
    ->name('ver.documento')
    ->middleware('auth');
