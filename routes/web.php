<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Public\Welcome;

use App\Livewire\Operacion\Home as OperacionHome;

use App\Livewire\Dashboard\Home as DashboardHome;

use App\Livewire\Admin\Home as AdminHome;
use App\Livewire\Admin\Panel;


Route::get('/', Welcome::class)->name('welcome');

Route::middleware(['auth'])->group(function () {
    Route::get('/operacion/home', OperacionHome::class)->name('operacion.home');
    Route::get('/dashboard/home', DashboardHome::class)->name('dashboard.home');
    Route::get('/admin/home', AdminHome::class)->middleware('can:admin-access')->name('admin.home');
});


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

    Route::prefix('barrios')->group(function () {
        Route::get('/', \App\Livewire\Admin\Barrios\Index::class)->name('barrios.index');
        Route::get('/create', \App\Livewire\Admin\Barrios\Create::class)->name('barrios.create');
        Route::get('/{barrios}/edit', \App\Livewire\Admin\Barrios\Edit::class)->name('barrios.edit');
        Route::get('/{barrios}', \App\Livewire\Admin\Barrios\Show::class)->name('barrios.show');
    });

    Route::prefix('ordenanzas')->group(function () {
        Route::get('/', \App\Livewire\Admin\Ordenanzas\Index::class)->name('ordenanzas.index');
        Route::get('/create', \App\Livewire\Admin\Ordenanzas\Create::class)->name('ordenanzas.create');
        Route::get('/{ordenanzas}/edit', \App\Livewire\Admin\Ordenanzas\Edit::class)->name('ordenanzas.edit');
        Route::get('/{ordenanzas}', \App\Livewire\Admin\Ordenanzas\Show::class)->name('ordenanzas.show');
    });    

    Route::prefix('salarios')->group(function () {
        Route::get('/', \App\Livewire\Admin\Salarios\Index::class)->name('salarios.index');
        Route::get('/create', \App\Livewire\Admin\Salarios\Create::class)->name('salarios.create');
        Route::get('/{salarios}/edit', \App\Livewire\Admin\Salarios\Edit::class)->name('salarios.edit');
        Route::get('/{salarios}', \App\Livewire\Admin\Salarios\Show::class)->name('salarios.show');
    });    

    Route::prefix('porcentajes')->group(function () {
        Route::get('/', \App\Livewire\Admin\Porcentajes\Index::class)->name('porcentajes.index');
        Route::get('/create', \App\Livewire\Admin\Porcentajes\Create::class)->name('porcentajes.create');
        Route::get('/{porcentajes}/edit', \App\Livewire\Admin\Porcentajes\Edit::class)->name('porcentajes.edit');
        Route::get('/{porcentajes}', \App\Livewire\Admin\Porcentajes\Show::class)->name('porcentajes.show');
    });    



require __DIR__.'/auth.php';


