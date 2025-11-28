<?php

namespace App\Livewire\Admin\Porcentajes;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\PorcentajeMultas;

#[Layout('layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Ordenamiento
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $confirmingDelete = false;
    public $multaToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            // Alterna asc/desc
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete($id)
    {
        $this->multaToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        PorcentajeMultas::find($this->multaToDelete)?->delete();
        $this->confirmingDelete = false;
    }


    public function render()
    {
        $multas = PorcentajeMultas::with(['ordenanza332', 'salarioMinimo'])
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.porcentajes.index', compact('multas'));
    }
}
