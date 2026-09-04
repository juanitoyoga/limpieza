<?php

namespace App\Livewire\Operacion\ContratosServicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ContratoServicio;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Lista extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroAuthStatus = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';


    public int $perPage = 10;
    public array $opcionesPerPage = [10, 25, 50, 100];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFiltroAuthStatus()
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
        $this->resetPage();
    }

    public function render()
    {
        $contratos = ContratoServicio::query()
            ->with('proveedor', 'oferta')
            ->when($this->search, fn($q) => $q->where(function ($qq) {
                $qq->where('codigo', 'like', "%{$this->search}%")
                    ->orWhere('titulo', 'like', "%{$this->search}%");
            }))
            ->when($this->filtroAuthStatus, fn($q) => $q->where('auth_status', $this->filtroAuthStatus))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.operacion.contratos-servicios.lista', compact('contratos'));
    }
}
