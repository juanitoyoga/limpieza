<?php

namespace App\Livewire\Admin\Denuncias;

use App\Models\Barrio;
use App\Models\Ordenanza332;
use App\Models\Denuncia;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Lista extends Component
{
    use WithPagination;

    // Filtros recibidos por URL
    public $vecino_id = '';
    public $barrio_id = '';
    public $ordenanza332_id = '';

    public $fecha_inicio = '';
    public $fecha_fin = '';

    public $estado_revision = ''; // verificado | aprobado | rechazado
    public $fecha_revision_inicio = '';
    public $fecha_revision_fin = '';

    public $rol = ''; // role_name que aplicó la revisión

    // Tabla
    public string $sortField     = 'fecha_denuncia';
    public string $sortDirection = 'desc';
    public int    $perPage       = 10;

    // Modal eliminación
    public bool $confirmingDelete = false;
    public ?int $deleteId         = null;

    public function mount()
    {
        $this->vecino_id                = request('vecino_id', '');
        $this->ordenanza332_id          = request('ordenanza332_id', '');
        $this->fecha_revision_inicio    = request('fecha_revision_inicio', '');
        $this->fecha_revision_fin       = request('fecha_revision_fin', '');
        $this->estado_revision          = request('estado_revision', '');
        $this->rol                      = request('rol', '');
        $this->barrio_id                = request('barrio_id', '');
        $this->fecha_inicio             = request('fecha_inicio', '');
        $this->fecha_fin                = request('fecha_fin', '');
        \Log::info('Denuncias Lista montada con filtros', [
            'vecino_id'              => $this->vecino_id,
            'barrio_id'              => $this->barrio_id,
            'ordenanza332_id'        => $this->ordenanza332_id,
            'fecha_inicio'           => $this->fecha_inicio,
            'fecha_fin'              => $this->fecha_fin,
            'estado_revision'        => $this->estado_revision,
            'fecha_revision_inicio'  => $this->fecha_revision_inicio,
            'fecha_revision_fin'     => $this->fecha_revision_fin,
            'rol'                    => $this->rol,
        ]);
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
        Denuncia::findOrFail($this->deleteId)->delete();
        $this->confirmingDelete = false;
        $this->deleteId         = null;
        session()->flash('message', 'Denuncia eliminada correctamente.');
    }

    public function render()
    {
        $denuncias = Denuncia::query()
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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);


        \Log::info('Resultado del query de denuncias', [
            'cantidad' => $denuncias->count(),
            'datos' => $denuncias->toArray(),
        ]);

        return view('livewire.admin.denuncias.lista', compact('denuncias'));
    }
}
