<?php

namespace App\Livewire\Admin\Ordenanzas;

use Livewire\Component;

use Livewire\WithPagination;

use App\Models\Ordenanza332;

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
    public $ordenanzaToDelete = null;

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
        $this->ordenanzaToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        Ordenanza332::find($this->ordenanzaToDelete)?->delete();
        $this->confirmingDelete = false;
    }

    public function render()
    {
        $ordenanzas = Ordenanza332::query()
            ->where('codigo', 'LIKE', "%{$this->search}%")
            ->orWhere('tipo', 'LIKE', "%{$this->search}%")
            ->orWhere('descripcion', 'LIKE', "%{$this->search}%")
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.ordenanzas.index', compact('ordenanzas'));
    }
}
