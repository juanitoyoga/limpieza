<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Operacion\Proveedores\Lista as ProveedoresLista;
use App\Livewire\Operacion\Proveedores\Create as ProveedoresCreate;
use App\Livewire\Operacion\Proveedores\Edit as ProveedoresEdit;
use App\Livewire\Operacion\Proveedores\Show as ProveedoresShow;

use App\Livewire\Operacion\Resoluciones\Lista as ResolucionesLista;
use App\Livewire\Operacion\Resoluciones\Create as ResolucionesCreate;
use App\Livewire\Operacion\Resoluciones\Edit as ResolucionesEdit;
use App\Livewire\Operacion\Resoluciones\Show as ResolucionesShow;

use App\Livewire\Operacion\CatalogoServicios\Index as CatalogoIndex;
use App\Livewire\Operacion\CatalogoServicios\Form as CatalogoForm;

use App\Livewire\Operacion\Ofertas\Lista as OfertasLista;
use App\Livewire\Operacion\Ofertas\Create as OfertasCreate;
use App\Livewire\Operacion\Ofertas\Edit as OfertasEdit;
use App\Livewire\Operacion\Ofertas\Servicios as OfertasServicios;
use App\Livewire\Operacion\Ofertas\Verificar as OfertasVerificar;
use App\Livewire\Operacion\Ofertas\Aprobar as OfertasAprobar;
use App\Livewire\Operacion\Ofertas\Rechazar     as OfertasRechazar;

Route::middleware(['auth'])->prefix('operacion')->group(function () {

    /* -----------------------------------------
       LISTA DE OFERTAS
    ------------------------------------------*/
    Route::get('/ofertas', OfertasLista::class)
        ->name('ofertas.lista');

    /* -----------------------------------------
       CREAR OFERTA
    ------------------------------------------*/
    Route::get('/ofertas/crear', OfertasCreate::class)
        ->name('ofertas.create');

    /* -----------------------------------------
       EDITAR OFERTA (solo si está Pendiente)
    ------------------------------------------*/
    Route::get('/ofertas/{oferta}/editar', OfertasEdit::class)
        ->name('ofertas.edit');

    /* -----------------------------------------
       SERVICIOS DE LA OFERTA
       (agregar, editar, eliminar servicios)
    ------------------------------------------*/
    Route::get('/ofertas/{oferta}/servicios', OfertasServicios::class)
        ->name('ofertas.servicios');

    /* -----------------------------------------
       VERIFICAR OFERTA
       (solo si está Pendiente)
    ------------------------------------------*/
    Route::get('/ofertas/{oferta}/verificar', OfertasVerificar::class)
        ->name('ofertas.verificar');

    /* -----------------------------------------
       APROBAR OFERTA
       (solo si está Verificada)
    ------------------------------------------*/
    Route::get('/ofertas/{oferta}/aprobar', OfertasAprobar::class)
        ->name('ofertas.aprobar');

    /* -----------------------------------------
       RECHAZAR OFERTA
       (solo si está Verificada)
    ------------------------------------------*/
    Route::get('/ofertas/{oferta}/rechazar', OfertasRechazar::class)
        ->name('ofertas.rechazar');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/catalogo-servicios', CatalogoIndex::class)
        ->name('catalogo-servicios.index');

    Route::get('/catalogo-servicios/crear', CatalogoForm::class)
        ->name('catalogo-servicios.create');

    // Cambiamos {id} por {catalogo} para habilitar Model Binding
    Route::get('/catalogo-servicios/{catalogo}/editar', CatalogoForm::class)
        ->name('catalogo-servicios.edit');
});
// PROVEEDORES
Route::prefix('proveedores')->name('proveedores.')->group(function () {
    Route::get('/', ProveedoresLista::class)->name('lista');
    Route::get('/crear', ProveedoresCreate::class)->name('create');
    Route::get('/{proveedor}/editar', ProveedoresEdit::class)->name('edit');
    Route::get('/{proveedor}/show', ProveedoresShow::class)->name('show');
});

// RESOLUCIONES
Route::prefix('resoluciones')->name('resoluciones.')->group(function () {
    Route::get('/', ResolucionesLista::class)->name('lista');
    Route::get('/crear', ResolucionesCreate::class)->name('create');
    Route::get('/{resolucion}/editar', ResolucionesEdit::class)->name('edit');
    Route::get('/{resolucion}/show', ResolucionesShow::class)->name('show');
    Route::get('/{resolucion}/verificar', \App\Livewire\Operacion\Resoluciones\Verificar::class)->name('verificar');
    Route::get('/{resolucion}/aprobar', \App\Livewire\Operacion\Resoluciones\Aprobar::class)->name('aprobar');
    Route::get('/{resolucion}/rechazar', \App\Livewire\Operacion\Resoluciones\Rechazar::class)->name('rechazar');
});

// DENUNCIAS
Route::prefix('denuncias')->group(function () {
    Route::get('/', \App\Livewire\Operacion\Denuncias\Index::class)
        ->name('denuncias.index');

    Route::get('/lista', \App\Livewire\Operacion\Denuncias\Lista::class)
        ->name('denuncias.lista');

    Route::get('/{id}', \App\Livewire\Operacion\Denuncias\Show::class)
        ->name('denuncias.show');
});

// NOTIFICACIONES
Route::prefix('notificaciones')->group(function () {
    Route::get('/', \App\Livewire\Operacion\Notificaciones\Index::class)
        ->name('notificaciones.index');

    Route::get('/lista', \App\Livewire\Operacion\Notificaciones\Lista::class)
        ->name('notificaciones.lista');

    Route::get('/{id}', \App\Livewire\Operacion\Notificaciones\Show::class)
        ->name('notificaciones.show');
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
