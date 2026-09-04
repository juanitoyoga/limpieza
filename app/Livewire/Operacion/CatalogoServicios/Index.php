<?php

namespace App\Livewire\Operacion\CatalogoServicios;

use App\Models\CatalogoServicios;
use App\Models\ServiceType;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTipo = ''; // guarda service_type_id como string
    public string $filterEstado = '';

    public string $sortField = 'orden';
    public string $sortDirection = 'asc';

    public int $perPage = 10;
    public array $opcionesPerPage = [10, 25, 50, 100];

    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    protected $queryString = ['search', 'filterTipo', 'filterEstado', 'perPage'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTipo(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEstado(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->confirmingDelete = false;
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            $this->cancelDelete();
            return;
        }

        $servicio = CatalogoServicios::findOrFail($this->deleteId);

        // Chequeo a nivel de aplicación: como el modelo usa SoftDeletes, ->delete()
        // hace un UPDATE (deleted_at), no un DELETE real. Un FK restrictOnDelete()
        // en resolucion_servicios NUNCA se dispararía con un soft delete, así que
        // ya no podemos depender solo de capturar QueryException para esto.
        if ($servicio->resolucionServicios()->exists()) {
            session()->flash('error', 'No se puede eliminar: este servicio ya está referenciado en una o más resoluciones.');
            $this->cancelDelete();
            return;
        }

        try {
            $servicio->delete();
            session()->flash('message', 'Servicio eliminado correctamente.');
        } catch (QueryException) {
            // Red de seguridad adicional por si en el futuro se agrega otra
            // referencia (p. ej. ofertas) que sí use un DELETE real.
            session()->flash('error', 'No se puede eliminar: este servicio ya está referenciado en otro registro.');
        }

        $this->cancelDelete();
        $this->resetPage();
    }

    public function render()
    {
        $items = CatalogoServicios::query()
            ->with(['serviceType', 'serviceSubtype', 'serviceScope', 'frequency'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', "%{$this->search}%")
                        ->orWhere('nombre', 'like', "%{$this->search}%")
                        ->orWhereHas('serviceType', fn($qt) => $qt->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('serviceSubtype', fn($qs) => $qs->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('serviceScope', fn($qa) => $qa->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterTipo, fn($query) => $query->where('service_type_id', $this->filterTipo))
            ->when($this->filterEstado !== '', fn($query) => $query->where('estado', $this->filterEstado === 'activo'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.operacion.catalogo-servicios.index', [
            'items' => $items,
            'tiposDisponibles' => ServiceType::where('active', true)->orderBy('name')->get(),
        ]);
    }
}
