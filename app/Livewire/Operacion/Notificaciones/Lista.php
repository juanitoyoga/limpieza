<?php

namespace App\Livewire\Operacion\Notificaciones;

use App\Models\Barrio;
use App\Models\Ordenanza332;
use App\Models\Notificacion;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Lista extends Component
{
    use WithPagination;

    // Filtros recibidos por URL
    public $contribuyente = '';
    public $barrio_id = '';
    public $ordenanza332_id = '';

    public $fecha_inicio = '';
    public $fecha_fin = '';

    public $estado_revision = '';
    public $fecha_revision_inicio = '';
    public $fecha_revision_fin = '';

    public $rol = '';

    // Tabla
    public string $sortField     = 'fecha_notificacion';
    public string $sortDirection = 'desc';
    public int    $perPage       = 10;

    // Modal eliminación
    public bool $confirmingDelete = false;
    public ?int $deleteId         = null;

    public function mount()
    {
        $this->contribuyente            = request('contribuyente', '');
        $this->ordenanza332_id          = request('ordenanza332_id', '');
        $this->fecha_revision_inicio    = request('fecha_revision_inicio', '');
        $this->fecha_revision_fin       = request('fecha_revision_fin', '');
        $this->estado_revision          = request('estado_revision', '');
        $this->rol                      = request('rol', '');
        $this->barrio_id                = request('barrio_id', '');
        $this->fecha_inicio             = request('fecha_inicio', '');
        $this->fecha_fin                = request('fecha_fin', '');
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId         = $id;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        Notificacion::findOrFail($this->deleteId)->delete();
        $this->confirmingDelete = false;
        $this->deleteId         = null;
        session()->flash('message', 'Notificación eliminada correctamente.');
    }

    public function render()
    {
        $notificaciones = Notificacion::query()
            ->with([
                'denuncia',
                'user',
                'barrio',
                'ordenanza332',
                'barrioAtributo',

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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.operacion.notificaciones.lista', compact('notificaciones'));
    }
}
