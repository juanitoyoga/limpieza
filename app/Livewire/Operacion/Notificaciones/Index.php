<?php

namespace App\Livewire\Operacion\Notificaciones;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notificacion;

use App\Models\Barrio;
use App\Models\Ordenanza332;
use App\Models\Role;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Index extends Component
{
    use WithPagination;

    // ─── Filtros y búsqueda ───────────────────────────────────────────────
    public $contribuyente = ''; // busca por nombre o identificación (snapshot)
    public $barrio_id = '';
    public $ordenanza332_id = '';

    public $fecha_inicio = '';
    public $fecha_fin = '';

    public $estado_revision = ''; // Pendiente | Enviada | Verificada | Aprobada | Rechazada | Vencida | Cerrada
    public $fecha_revision_inicio = '';
    public $fecha_revision_fin = '';

    public $rol = ''; // role_name que aplicó la revisión

    // Modal eliminación
    public bool $confirmingDelete = false;
    public ?int $deleteId         = null;

    // ─── Tabla ─────────────────────────────────────────────────────────────
    public $perPage = 10;
    public $sortField = 'fecha_notificacion';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($property)
    {
        if (!in_array($property, ['sortField', 'sortDirection', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function buscar()
    {
        $this->resetPage();
    }

    public function limpiar()
    {
        $this->reset([
            'contribuyente',
            'barrio_id',
            'ordenanza332_id',
            'estado_revision',
            'fecha_inicio',
            'fecha_fin',
            'fecha_revision_inicio',
            'fecha_revision_fin',
            'rol',
        ]);

        $this->resetPage();
    }

    private function buildQuery()
    {
        return Notificacion::query()
            ->with([
                'denuncia',
                'user',
                'barrio',
                'ordenanza332',
                'barrioAtributo',

                // Relaciones necesarias para el accessor revisor
                'verificadoPorFuncionario.user',
                'aprobadoPorFuncionario.user',
                'rechazadoPorFuncionario.user',

                'verificadoPorSupervisor.user',
                'aprobadoPorSupervisor.user',
                'rechazadoPorSupervisor.user',
            ])
            ->when($this->contribuyente, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('contribuyente_nombre', 'like', "%{$this->contribuyente}%")
                        ->orWhere('contribuyente_identificacion', 'like', "%{$this->contribuyente}%");
                });
            })
            ->when($this->barrio_id, fn($q) => $q->where('barrio_id', $this->barrio_id))
            ->when($this->ordenanza332_id, fn($q) => $q->where('ordenanza332_id', $this->ordenanza332_id))
            ->when($this->fecha_inicio, fn($q) => $q->whereDate('fecha_notificacion', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('fecha_notificacion', '<=', $this->fecha_fin))
            ->when($this->estado_revision, fn($q) => $q->where('estado', $this->estado_revision))
            ->when($this->fecha_revision_inicio || $this->fecha_revision_fin, function ($query) {
                $columnaFecha = match ($this->estado_revision) {
                    'Verificada' => 'verificado_at',
                    'Aprobada'   => 'aprobado_at',
                    'Rechazada'  => 'rechazado_at',
                    default      => null,
                };

                if ($columnaFecha) {
                    $query->when(
                        $this->fecha_revision_inicio,
                        fn($q) => $q->whereDate($columnaFecha, '>=', $this->fecha_revision_inicio)
                    )->when(
                        $this->fecha_revision_fin,
                        fn($q) => $q->whereDate($columnaFecha, '<=', $this->fecha_revision_fin)
                    );
                }
            })
            ->when($this->rol, function ($query) {
                $query->where(function ($q) {
                    $q->where('verificado_por_rol', $this->rol)
                        ->orWhere('aprobado_por_rol', $this->rol)
                        ->orWhere('rechazado_por_rol', $this->rol);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.operacion.notificaciones.index', [
            'notificaciones'  => $this->buildQuery()->paginate($this->perPage),
            'barrios'         => Barrio::activos()->orderBy('nombre')->get(),
            'contravenciones' => Ordenanza332::orderBy('codigo')->get(),
            'roles'           => Role::ordered()->get(),
        ]);
    }
}
