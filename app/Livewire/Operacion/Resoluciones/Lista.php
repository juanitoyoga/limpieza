<?php

namespace App\Livewire\Operacion\Resoluciones;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Resolucion, ServiceType};
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Lista extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroAuthStatus = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroAuthStatus()
    {
        $this->resetPage();
    }

    /**
     * Alterna la dirección de orden si se hace clic en la misma columna,
     * o cambia de columna con dirección ascendente por defecto.
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function render()
    {
        $resoluciones = Resolucion::query()
            ->with(['barrio', 'serviceType'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', "%{$this->search}%")
                        ->orWhere('titulo', 'like', "%{$this->search}%")
                        ->orWhere('service_type_id  ', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filtroAuthStatus, fn($q) => $q->where('auth_status', $this->filtroAuthStatus))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.operacion.resoluciones.lista', compact('resoluciones'));
    }
}
