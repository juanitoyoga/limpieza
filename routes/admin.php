<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BarrioController;

use App\Http\Controllers\VecinoController;

use App\Http\Controllers\BarrioAtributoController;


// BARRIOS
Route::prefix('barrios')->group(function () {

    // LISTADO
    Route::get('/', [BarrioController::class, 'index'])
        ->name('barrios.index');

    // LISTA — Livewire tabla paginada con filtros aplicados
    Route::get('/lista', \App\Livewire\Admin\Barrios\Lista::class)
        ->name('barrios.lista');
    // FORMULARIO CREAR
    Route::get('/create', [BarrioController::class, 'create'])
        ->name('barrios.create');

    // GUARDAR
    Route::post('/', [BarrioController::class, 'store'])
        ->name('barrios.store');

    // MOSTRAR
    Route::get('/{barrio}', [BarrioController::class, 'show'])
        ->name('barrios.show');

    // FORMULARIO EDITAR
    Route::get('/{barrio}/edit', [BarrioController::class, 'edit'])
        ->name('barrios.edit');

    // ACTUALIZAR
    Route::put('/{barrio}', [BarrioController::class, 'update'])
        ->name('barrios.update');

    // ELIMINAR
    Route::delete('/{barrio}', [BarrioController::class, 'destroy'])
        ->name('barrios.destroy');
});
// BARRIOS ATRIBUTOS
Route::prefix('barrio-atributo')->group(function () {

    // LISTADO (formulario de filtros)
    Route::get('/', [BarrioAtributoController::class, 'index'])
        ->name('barrio-atributo.index');

    // LISTA — Livewire tabla paginada con filtros aplicados
    Route::get('/lista', \App\Livewire\Admin\BarriosAtributos\Lista::class)
        ->name('barrio-atributo.lista');

    // FORMULARIO CREAR
    Route::get('/create', [BarrioAtributoController::class, 'create'])
        ->name('barrio-atributo.create');

    // GUARDAR
    Route::post('/', [BarrioAtributoController::class, 'store'])
        ->name('barrio-atributo.store');

    // FORMULARIO EDITAR
    Route::get('/{barrioAtributo}/edit', [BarrioAtributoController::class, 'edit'])
        ->name('barrio-atributo.edit');

    // FORMULARIO MOSTRAR
    Route::get('/{barrioAtributo}/show', [BarrioAtributoController::class, 'show'])
        ->name('barrio-atributo.show');

    // ACTUALIZAR
    Route::put('/{barrioAtributo}', [BarrioAtributoController::class, 'update'])
        ->name('barrio-atributo.update');
});

Route::prefix('vecinos')->group(function () {

    // FORMULARIO DE FILTROS (Blade normal)
    Route::get('/', [VecinoController::class, 'index'])
        ->name('vecinos.index');

    // LISTA — Livewire tabla paginada con filtros aplicados
    Route::get('/lista', \App\Livewire\Admin\Vecinos\Lista::class)
        ->name('vecinos.lista');

    // CREAR — Livewire
    Route::get('/create', \App\Livewire\Admin\Vecinos\Create::class)
        ->name('vecinos.create');

    // MOSTRAR — Livewire (si lo necesitas)
    Route::get('/{vecino}/show', \App\Livewire\Admin\Vecinos\Show::class)
        ->name('vecinos.show');

    // EDITAR — Livewire
    // Route::get('/{vecino}/edit', \App\Livewire\Admin\Vecinos\Edit::class)
    //     ->name('vecinos.edit');

    // ELIMINAR — Livewire o controlador (según tu preferencia)
    Route::delete('/{vecino}', [VecinoController::class, 'destroy'])
        ->name('vecinos.destroy');
});

//
// ORDENANZAS
//
Route::prefix('ordenanzas')->group(function () {
    Route::get('/index', \App\Livewire\Admin\Ordenanzas\Index::class)
        ->name('ordenanzas.index');

    Route::get('/create', \App\Livewire\Admin\Ordenanzas\Create::class)
        ->name('ordenanzas.create');

    Route::get('/ordenanzas/{id}/edit', \App\Livewire\Admin\Ordenanzas\Edit::class)
        ->whereNumber('ordenanzas')
        ->name('ordenanzas.edit');

    Route::get('/ordenanzas/{id}', \App\Livewire\Admin\Ordenanzas\Show::class)
        ->whereNumber('ordenanzas')
        ->name('ordenanzas.show');
});


//
// ROLES DE USUARIO
//
Route::prefix('userroles')->group(function () {
    Route::get('/index', \App\Livewire\Admin\Userroles\Index::class)
        ->name('userroles.index');

    Route::get('/create', \App\Livewire\Admin\Userroles\Create::class)
        ->name('userroles.create');

    Route::get('/userroles/{id}/edit', \App\Livewire\Admin\Userroles\Edit::class)
        ->whereNumber('userroles')
        ->name('userroles.edit');

    Route::get('/userroles/{id}', \App\Livewire\Admin\Userroles\Show::class)
        ->whereNumber('userroles')
        ->name('userroles.show');
});

//
// SALARIOS
//
Route::prefix('salarios')->group(function () {
    Route::get('/index', \App\Livewire\Admin\Salarios\Index::class)
        ->name('salarios.index');

    Route::get('/create', \App\Livewire\Admin\Salarios\Create::class)
        ->name('salarios.create');

    Route::get('/{salarios}/edit', \App\Livewire\Admin\Salarios\Edit::class)
        ->whereNumber('salarios')
        ->name('salarios.edit');

    Route::get('/{salarios}', \App\Livewire\Admin\Salarios\Show::class)
        ->whereNumber('salarios')
        ->name('salarios.show');
});

//
// PORCENTAJES
//
Route::prefix('porcentajes')->group(function () {
    Route::get('/index', \App\Livewire\Admin\Porcentajes\Index::class)
        ->name('porcentajes.index');

    Route::get('/create', \App\Livewire\Admin\Porcentajes\Create::class)
        ->name('porcentajes.create');

    Route::get('/{porcentajes}/edit', \App\Livewire\Admin\Porcentajes\Edit::class)
        ->whereNumber('porcentajes')
        ->name('porcentajes.edit');

    Route::get('/{porcentajes}/show', \App\Livewire\Admin\Porcentajes\Show::class)
        ->whereNumber('porcentajes')
        ->name('porcentajes.show');
});
//
// CONTRATOS
//
Route::prefix('contratos')->group(function () {

    Route::get('/', \App\Livewire\Admin\Contratos\Index::class)
        ->name('contratos.index');

    Route::get('/create', \App\Livewire\Admin\Contratos\Create::class)
        ->name('contratos.create');

    Route::get('/{contrato}/edit', \App\Livewire\Admin\Contratos\Edit::class)
        ->whereNumber('contrato')
        ->name('contratos.edit');

    Route::get('/{contrato}', \App\Livewire\Admin\Contratos\Show::class)
        ->whereNumber('contrato')
        ->name('contratos.show');
});



//
// WEB3
//
Route::prefix('web3')->group(function () {
    Route::get('/block-number', [\App\Http\Controllers\Web3Controller::class, 'blockNumber']);
    Route::get('/balance', [\App\Http\Controllers\Web3Controller::class, 'getBalance']);
    Route::get('/gas-price', [\App\Http\Controllers\Web3Controller::class, 'getGasPrice']);
    Route::get('/transaction', [\App\Http\Controllers\Web3Controller::class, 'getTransaction']);
});
