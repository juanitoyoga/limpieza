<?php

namespace App\Livewire\Operacion\Proveedores;

use App\Models\Proveedor;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Lista extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtroEstado = '';
    public string $sortField = 'razon_social';
    public string $sortDirection = 'asc';

    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    protected $queryString = ['search', 'filtroEstado'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
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

    public function delete(): void
    {
        Proveedor::findOrFail($this->deleteId)->delete();

        $this->confirmingDelete = false;
        $this->deleteId = null;

        session()->flash('message', 'Proveedor eliminado correctamente.');
    }

    public function render()
    {
        $proveedores = Proveedor::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('razon_social', 'like', "%{$this->search}%")
                        ->orWhere('ruc', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filtroEstado, fn($query) => $query->where('estado', $this->filtroEstado))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.operacion.proveedores.lista', [
            'proveedores' => $proveedores,
        ]);
    }
}
