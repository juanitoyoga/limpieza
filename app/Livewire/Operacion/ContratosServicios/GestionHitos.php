<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\ContratoServicio;
use App\Models\ContratoServicioDetalle;
use App\Models\HitoContratoServicio;
use App\Models\EvidenciaHito;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\{Auth, Gate, DB};

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

use Illuminate\Auth\Access\AuthorizationException;

#[Layout('layouts.operacion')]
class GestionHitos extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public ?int $contratoId = null;

    // Modales y Acciones
    public bool $showActionModal = false;
    public string $actionType = ''; // 'verificar', 'aprobar', 'rechazar', 'revisar'
    public ?int $hitoSeleccionadoId = null;
    public ?int $detalleSeleccionadoId = null;

    // Formulario Modal
    public string $observaciones = '';
    public bool $acepta_responsabilidad = false;

    protected $queryString = ['contratoId'];

    public function mount(): void
    {
        // No se requiere nada adicional: contratoId ya viene hidratado
        // desde el queryString y contratoSeleccionado() lo resuelve on-demand.
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Computed: se resuelve por request y NO viaja en el snapshot serializado
     * de Livewire entre round-trips. Evita cargar el árbol completo de
     * relaciones (proveedor, detalles, hitos, evidencias, usuarios) en cada
     * click/actualización del componente.
     */
    #[Computed]
    public function contratoSeleccionado(): ?ContratoServicio
    {
        if (! $this->contratoId) {
            return null;
        }

        return ContratoServicio::with([
            'proveedor',
            'detalles.catalogoServicio',
            'detalles.hito.evidencias',
            'detalles.hito.verificadoPor',
            'detalles.hito.aprobadoPor',
            'detalles.hito.rechazadoPor',
        ])->find($this->contratoId);
    }

    // #[Computed]
    // public function detalleSeleccionado(): ?ContratoServicioDetalle
    // {
    //     if (! $this->detalleSeleccionadoId) {
    //         return null;
    //     }

    //     return ContratoServicioDetalle::with(['catalogoServicio', 'hito.evidencias'])
    //         ->find($this->detalleSeleccionadoId);
    // }

    // #[Computed]
    // public function hitoSeleccionado(): ?HitoContratoServicio
    // {
    //     if (! $this->hitoSeleccionadoId) {
    //         return null;
    //     }

    //     return HitoContratoServicio::with('evidencias')->find($this->hitoSeleccionadoId);
    // }
    #[Computed]
    public function detalleSeleccionado(): ?ContratoServicioDetalle
    {
        if (! $this->detalleSeleccionadoId) {
            return null;
        }

        return ContratoServicioDetalle::with([
            'catalogoServicio',
            'contratoServicio.resolucion.barrio',
            'contratoServicio.proveedor',
            'hito.evidencias.capturadoPor',
            'hito.verificadoPor',
            'hito.aprobadoPor',
            'hito.rechazadoPor',
        ])->find($this->detalleSeleccionadoId);
    }

    #[Computed]
    public function hitoSeleccionado(): ?HitoContratoServicio
    {
        if (! $this->hitoSeleccionadoId) {
            return null;
        }

        return HitoContratoServicio::with([
            'evidencias.capturadoPor',
            'verificadoPor',
            'aprobadoPor',
            'rechazadoPor',
            'creadoPor',
            'contratoServicio.proveedor',
        ])->find($this->hitoSeleccionadoId);
    }
    public function seleccionarContrato(int $id): void
    {
        $this->contratoId = $id;
        unset($this->contratoSeleccionado); // limpia cache del Computed para este request
    }

    public function limpiarSeleccion(): void
    {
        $this->contratoId = null;
        unset($this->contratoSeleccionado);
    }

    public function abrirModal(int $detalleId, string $accion): void
    {
        $this->resetValidation();
        $this->reset(['observaciones', 'acepta_responsabilidad']);

        $detalle = ContratoServicioDetalle::findOrFail($detalleId);

        $this->detalleSeleccionadoId = $detalle->id;
        $this->hitoSeleccionadoId = $detalle->hito?->id;
        $this->actionType = $accion;
        $this->showActionModal = true;

        unset($this->detalleSeleccionado, $this->hitoSeleccionado);
    }

    public function cerrarModal(): void
    {
        $this->showActionModal = false;
        $this->hitoSeleccionadoId = null;
        $this->detalleSeleccionadoId = null;

        unset($this->detalleSeleccionado, $this->hitoSeleccionado);
    }

    public function procesarAccion(): void
    {
        \Log::debug('[MEM] inicio procesarAccion: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

        $user = Auth::user();

        // 1. Validaciones de Formulario
        if (in_array($this->actionType, ['verificar', 'aprobar', 'rechazar'], true)) {
            $this->validate([
                'observaciones'          => 'required|string|min:5',
                'acepta_responsabilidad' => 'accepted',
            ]);
        }

        \Log::debug('[MEM] tras validate: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

        $detalle = $this->detalleSeleccionado;
        $hito = $this->hitoSeleccionado;

        \Log::debug('[MEM] tras cargar detalle/hito: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

        try {
            // 2. Control de Autorización mediante Gates
            if ($this->actionType === 'verificar') {
                Gate::authorize('iniciarverificacion', $detalle);
            } elseif ($this->actionType === 'aprobar') {
                Gate::authorize('aprobar-hito', $hito);
            } elseif ($this->actionType === 'rechazar') {
                Gate::authorize('aprobar-hito', $hito);
            }

            \Log::debug('[MEM] tras Gate::authorize: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

            // 3. Ejecución de la Transacción en BD
            DB::transaction(function () use ($user, $detalle, $hito) {
                \Log::debug('[MEM] dentro de transaction, actionType=' . $this->actionType . ': ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

                if ($this->actionType === 'verificar') {
                    $nuevoHito = HitoContratoServicio::create([
                        'contratos_servicios_id'       => $this->contratoId,
                        'contrato_servicio_detalle_id' => $detalle->id,
                        'capturado_en_campo_at'        => $detalle->fecha_hito ?? now(),
                        'descripcion_servicio'         => $this->observaciones,
                        'user_id'                       => $user->id,
                        'verificado_por'               => $user->id,
                        'verificado_at'                => now(),
                    ]);

                    // Asociar las evidencias existentes al nuevo hito
                    EvidenciaHito::where('contrato_servicio_detalle_id', $detalle->id)
                        ->update(['hitos_contrato_servicio_id' => $nuevoHito->id]);

                    AuditEvent::logEvent($nuevoHito, $user->id, 'HITO_VERIFICADO', ['message' => $this->observaciones]);
                } elseif ($this->actionType === 'aprobar') {
                    \Log::debug('[MEM] antes de hito->update (aprobar): ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

                    $hito->update([
                        'aprobado_por' => $user->id,
                        'aprobado_at'  => now(),
                    ]);

                    \Log::debug('[MEM] tras hito->update, Observer ya corrió: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

                    AuditEvent::logEvent(
                        $hito,
                        $user->id,
                        'HITO_APROBADO',
                        ['message' => $this->observaciones]
                    );

                    \Log::debug('[MEM] tras AuditEvent::logEvent: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

                    // El HitoContratoServicioObserver ya recalcula el hash y
                    // despacha el job de blockchain en su método updated().
                    // No es necesario invocarlo aquí de nuevo.
                } elseif ($this->actionType === 'rechazar') {
                    $hito->update([
                        'rechazado_por' => $user->id,
                        'rechazado_at'  => now(),
                    ]);

                    AuditEvent::logEvent(
                        $hito,
                        $user->id,
                        'HITO_RECHAZADO',
                        ['message' => $this->observaciones]
                    );
                }
            });

            \Log::debug('[MEM] tras DB::transaction completa: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');

            session()->flash('success', 'Operación registrada correctamente.');
            $this->cerrarModal();
            unset($this->contratoSeleccionado);

            \Log::debug('[MEM] fin procesarAccion: ' . number_format(memory_get_usage(true) / 1048576, 2) . ' MB');
        } catch (AuthorizationException $e) {
            $this->addError('global', $e->getMessage() ?: 'No tienes permisos para realizar esta acción en este barrio.');
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'procesar_accion_hito', $e);
            $this->addError('global', 'Ocurrió un error inesperado en el sistema.');
        }
    }

    public function render()
    {

        $userRole = Auth::user()->role_name ?? '';
        // En render(), agregar el filtro de barrio a la query de $contratosVigentes:
        $user = Auth::user();

        $contratosVigentes = ContratoServicio::query()
            ->where('auth_status', ContratoServicio::ESTADO_APROBADA)
            ->when(
                in_array($user->role_name, ['Dirigente', 'Presidente'], true),
                fn($q) => $q->whereHas('resolucion', fn($qq) => $qq->where('barrio_id', $user->barrioComoResponsable()))
            )
            ->when($this->search, fn($q) => $q->where('codigo', 'like', "%{$this->search}%")
                ->orWhere('titulo', 'like', "%{$this->search}%"))
            ->paginate(8);

        return view('livewire.operacion.contratos-servicios.gestionhitos', [
            'contratosVigentes' => $contratosVigentes,
            'esPresidente' => $userRole === 'Presidente',
            'esDirigente' => $userRole === 'Dirigente',
        ]);
    }
}
