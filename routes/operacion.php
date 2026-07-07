<?php

use Illuminate\Support\Facades\Route;

// DENUNCIAS
Route::prefix('denuncias')->group(function () {
    // FORMULARIO DE FILTROS (Blade normal)
    Route::get('/', \App\Livewire\Operacion\Denuncias\Index::class)
        ->name('denuncias.index');

    // LISTA — Livewire tabla paginada con filtros aplicados
    Route::get('/lista', \App\Livewire\Operacion\Denuncias\Lista::class)
        ->name('denuncias.lista');

    // LISTA — Livewire tabla paginada con filtros aplicados
    Route::get('/denuncias/{id}', \App\Livewire\Operacion\Denuncias\Show::class)
        ->name('denuncias.show');
});

// Nominations

Route::prefix('nominations')->group(function () {
    Route::get('/index', \App\Livewire\Operacion\Nominations\Index::class)->name('nominations.index');
    Route::get('/create', \App\Livewire\Operacion\Nominations\Create::class)->name('nominations.create');

    // Rutas con ID (Asegúrate de que no se repita la URL base)
    Route::get('/{id}/edit', \App\Livewire\Operacion\Nominations\Edit::class)->name('nominations.edit');
    Route::get('/{id}/show', \App\Livewire\Operacion\Nominations\Show::class)->name('nominations.show'); // Cambié la URL para evitar conflicto
    Route::get('/{id}/verificar', \App\Livewire\Operacion\Nominations\Verificar::class)->name('nominations.verificar');
    Route::get('/{id}/aprobar', \App\Livewire\Operacion\Nominations\Aprobar::class)->name('nominations.aprobar');
    Route::get('/{id}/rechazar', \App\Livewire\Operacion\Nominations\Rechazar::class)->name('nominations.rechazar');
    Route::get('/{id}/imprimir', \App\Livewire\Operacion\Nominations\Imprimir::class)->name('nominations.imprimir'); // URL única
});
Route::prefix('assignments')->group(function () {
    Route::get('/index', \App\Livewire\Operacion\Asignments\Index::class)->name('assignments.index');
    Route::get('/create', \App\Livewire\Operacion\Asignments\Create::class)->name('assignments.create');
    Route::get('/{assignments}/update', \App\Livewire\Operacion\Asignments\Update::class)->name('assignments.update');
    Route::get('/{assignments}', \App\Livewire\Operacion\Asignments\Show::class)->name('assignments.show');
});

Route::prefix('verifications')->group(function () {
    Route::get('/index', \App\Livewire\Operacion\Verifications\Index::class)->name('verifications.index');
    Route::get('/create', \App\Livewire\Operacion\Verifications\Create::class)->name('verifications.create');
    Route::get('/{verifications}/update', \App\Livewire\Operacion\Verifications\Update::class)->name('verifications.update');
    Route::get('/{verifications}', \App\Livewire\Operacion\Verifications\Show::class)->name('verifications.show');
});

// Approvals
Route::prefix('approvals')->group(function () {
    Route::get('/index', \App\Livewire\Operacion\Approvals\Index::class)->name('approvals.index');
    Route::get('/create', \App\Livewire\Operacion\Approvals\Create::class)->name('approvals.create');
    Route::get('/{approvals}/update', \App\Livewire\Operacion\Approvals\Update::class)->name('approvals.update');
    Route::get('/{approvals}', \App\Livewire\Operacion\Approvals\Show::class)->name('approvals.show');
});

// Audit Events
Route::prefix('auditevents')->group(function () {
    Route::get('/', \App\Livewire\Operacion\AuditEvents\Index::class)->name('auditevents.index');
    // Route::get('/{auditevents}/update', \App\Livewire\Operacion\AuditEvents\Update::class)->name('auditevents.update');
    Route::get('/{auditevents}', \App\Livewire\Operacion\AuditEvents\Show::class)->name('auditevents.show');
});
