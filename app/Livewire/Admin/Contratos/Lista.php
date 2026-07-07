<?php

namespace App\Livewire\Admin\Contratos;

use App\Models\Contrato;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]

class Lista extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $confirmingDelete = false;
    public $deleteId;

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        Contrato::findOrFail($this->deleteId)->delete();
        $this->confirmingDelete = false;

        session()->flash('message', 'Contrato eliminado correctamente.');
    }

    public function render()
    {
        $contratos = Contrato::with('barrio')
            ->where(function ($q) {
                $q->where('numero_contrato', 'like', "%{$this->search}%")
                    ->orWhere('estado', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'barrio',
                        fn($b) =>
                        $b->where('nombre', 'like', "%{$this->search}%")
                    );
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.contratos.index', compact('contratos'));
    }
}
