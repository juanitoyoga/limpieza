<?php

namespace App\Livewire\Operacion\CatalogoServicios;

use App\Models\CatalogoServicios;
use Illuminate\Database\QueryException;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTipo = '';
    public string $filterEstado = '';

    public string $sortField = 'orden';
    public string $sortDirection = 'asc';

    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    protected $queryString = ['search', 'filterTipo', 'filterEstado'];

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
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', "%{$this->search}%")
                        ->orWhere('nombre', 'like', "%{$this->search}%")
                        ->orWhere('tipo', 'like', "%{$this->search}%")
                        ->orWhere('subtipo', 'like', "%{$this->search}%")
                        ->orWhere('ambito', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterTipo, fn($query) => $query->where('tipo', $this->filterTipo))
            ->when($this->filterEstado !== '', fn($query) => $query->where('estado', $this->filterEstado === 'activo'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.operacion.catalogo-servicios.index', [
            'items' => $items,
            'tiposDisponibles' => CatalogoServicios::query()
                ->select('tipo')
                ->distinct()
                ->orderBy('tipo')
                ->pluck('tipo'),
        ]);
    }
}
