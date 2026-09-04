<?php

namespace App\Livewire\Operacion\Denuncias;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Denuncia;

use App\Models\Barrio;
use App\Models\Ordenanza332;
use App\Models\Role;
use App\Models\Vecino;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.operacion')]
class Index extends Component
{
    use WithPagination;

    // ─── Filtros y búsqueda ───────────────────────────────────────────────
    #[Url(keep: true)]
    public $vecino_id = '';
    #[Url(keep: true)]
    public $barrio_id = '';
    #[Url(keep: true)]
    public $ordenanza332_id = '';

    #[Url(keep: true)]
    public $fecha_inicio = '';
    #[Url(keep: true)]
    public $fecha_fin = '';

    #[Url(keep: true)]
    public $estado_revision = ''; // verificado | aprobado | rechazado
    #[Url(keep: true)]
    public $fecha_revision_inicio = '';
    #[Url(keep: true)]
    public $fecha_revision_fin = '';

    #[Url(keep: true)]
    public $rol = ''; // role_name que aplicó la revisión


    // Modal eliminación
    public bool $confirmingDelete = false;
    public ?int $deleteId         = null;

    // ─── Tabla ─────────────────────────────────────────────────────────────
    #[Url(keep: true)]
    public $perPage = 10;
    #[Url(keep: true)]
    public $sortField = 'fecha_denuncia';
    #[Url(keep: true)]
    public $sortDirection = 'desc';

    // ─── Anulación ─────────────────────────────────────────────────────────
    public $confirmingAnulacion = false;
    public $denunciaToAnular = null;

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

    // ─── Filtros avanzados ────────────────────────────────────────────────
    public function buscar()
    {
        $this->resetPage();
    }

    public function limpiar()
    {
        $this->reset([
            'estado_revision',
            'fecha_inicio',
            'fecha_fin',
        ]);

        $this->resetPage();
    }

    // ─── Anulación ─────────────────────────────────────────────────────────
    public function confirmAnular($id)
    {
        $this->denunciaToAnular = $id;
        $this->confirmingAnulacion = true;
    }

    public function anular()
    {
        $denuncia = Denuncia::find($this->denunciaToAnular);

        if ($denuncia && $denuncia->estado !== Denuncia::ESTADO_ANULADA) {
            $denuncia->update(['estado' => Denuncia::ESTADO_ANULADA]);
        }

        $this->confirmingAnulacion = false;
        $this->denunciaToAnular = null;

        session()->flash('success', 'Trámite anulado correctamente.');
    }

    private function buildQuery()
    {
        return Denuncia::query()
            ->with([
                'vecino.user',
                'barrio',
                'ordenanza332',

                // Relaciones necesarias para el accessor revisor
                'verificadoPorDirigente.user',
                'aprobadoPorDirigente.user',
                'rechazadoPorDirigente.user',

                'verificadoPorFuncionario.user',
                'aprobadoPorFuncionario.user',
                'rechazadoPorFuncionario.user',

                'verificadoPorSupervisor.user',
                'aprobadoPorSupervisor.user',
                'rechazadoPorSupervisor.user',
            ])
            ->when($this->vecino_id, fn($q) => $q->where('vecino_id', $this->vecino_id))
            ->when($this->barrio_id, fn($q) => $q->where('barrio_id', $this->barrio_id))
            ->when($this->ordenanza332_id, fn($q) => $q->where('ordenanza332_id', $this->ordenanza332_id))
            ->when($this->fecha_inicio, fn($q) => $q->whereDate('fecha_denuncia', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('fecha_denuncia', '<=', $this->fecha_fin))
            ->when($this->estado_revision, fn($q) => $q->where('estado', $this->estado_revision))
            ->when($this->fecha_revision_inicio || $this->fecha_revision_fin, function ($query) {
                $columnaFecha = match ($this->estado_revision) {
                    'Verificado' => 'verificado_at',
                    'Aprobado'   => 'aprobado_at',
                    'Rechazado'  => 'rechazado_at',
                    default      => null,
                };

                if ($columnaFecha) {
                    $query->when(
                        $this->fecha_revision_inicio,
                        fn($q) =>
                        $q->whereDate($columnaFecha, '>=', $this->fecha_revision_inicio)
                    )->when(
                        $this->fecha_revision_fin,
                        fn($q) =>
                        $q->whereDate($columnaFecha, '<=', $this->fecha_revision_fin)
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
        return view('livewire.operacion.denuncias.index', [
            'denuncias' => $this->buildQuery()->paginate($this->perPage),
            'vecinos' => Vecino::with('user')
                ->join('users', 'vecinos.user_id', '=', 'users.id')
                ->orderBy('users.first_name')
                ->orderBy('users.last_name')
                ->select('vecinos.*')
                ->get(),
            'barrios'         => Barrio::activos()->orderBy('nombre')->get(),
            'contravenciones' => Ordenanza332::orderBy('codigo')->get(),
            'roles'           => Role::ordered()->get(),
        ]);
    }
}
