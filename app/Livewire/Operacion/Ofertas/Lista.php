<?php

namespace App\Livewire\Operacion\Ofertas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Oferta;
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
        $ofertas = Oferta::query()
            ->with(['proveedor', 'resolucion'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', "%{$this->search}%")
                        ->orWhere('titulo', 'like', "%{$this->search}%")
                        ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filtroAuthStatus, fn($q) => $q->where('auth_status', $this->filtroAuthStatus))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.operacion.ofertas.lista', compact('ofertas'));
    }
}
