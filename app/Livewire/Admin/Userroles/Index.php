<?php

namespace App\Livewire\Admin\Userroles;

use Livewire\Component;

use Livewire\WithPagination;

use App\Models\Userrole;

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
    public $userroleToDelete = null;

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
        $this->userroleToDelete = $id;
        $this->confirmingDelete = true;
    }

    public function delete()
    {
        Userrole::find($this->userroleToDelete)?->delete();
        $this->confirmingDelete = false;
    }

    public function render()
    {
        $userroles = Userrole::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'LIKE', "%{$this->search}%")
                      ->orWhere('tipo', 'LIKE', "%{$this->search}%")
                      ->orWhere('descripcion', 'LIKE', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    
        return view('livewire.admin.userroles.index', compact('userroles'));
    }
    
    
    
}
