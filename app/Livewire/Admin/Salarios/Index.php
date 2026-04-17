<?php

namespace App\Livewire\Admin\Salarios;

use Livewire\Component;

use Livewire\WithPagination;

use App\Models\SalarioMinimo;

use Livewire\Attributes\Layout;


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
    public $salariominimoToDelete = null;

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
        $this->salariominimoToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        SalarioMinimo::find($this->salariominimoToDelete)?->delete();
        $this->confirmingDelete = false;
    }

    public function render()
    {
        $salarios = SalarioMinimo::query()
            ->where('year', 'LIKE', "%{$this->search}%")
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.salarios.index', compact('salarios'));
    }
}
