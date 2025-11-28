<?php

namespace App\Livewire\Admin\Barrios;

use Livewire\Component;

use Livewire\WithPagination;

use App\Models\Barrio;

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
    public $barrioToDelete = null;

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
        $this->barrioToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        Barrio::find($this->barrioToDelete)?->delete();
        $this->confirmingDelete = false;
    }

    public function render()
    {
        $barrios = Barrio::query()
            ->where('nombre', 'LIKE', "%{$this->search}%")
            ->orWhere('sector', 'LIKE', "%{$this->search}%")
            ->orWhere('parroquia', 'LIKE', "%{$this->search}%")
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.barrios.index', compact('barrios'));
    }
}
