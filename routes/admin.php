<?php

use Illuminate\Support\Facades\Route;

//
// BARRIOS
//
Route::prefix('barrios')->group(function () {
    Route::get('/index', \App\Livewire\Admin\Barrios\Index::class)
        ->name('barrios.index');

    Route::get('/create', \App\Livewire\Admin\Barrios\Create::class)
        ->name('barrios.create');

        
    Route::get('/barrios/{id}/edit', \App\Livewire\Admin\Barrios\Edit::class)
        ->name('barrios.edit');

    Route::get('/barrios/{id}', \App\Livewire\Admin\Barrios\Show::class)
        ->name('barrios.show');
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

    Route::get('/{porcentajes}', \App\Livewire\Admin\Porcentajes\Show::class)
        ->whereNumber('porcentajes')
        ->name('porcentajes.show');
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
