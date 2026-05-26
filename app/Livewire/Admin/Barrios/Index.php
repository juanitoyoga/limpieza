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

    public string $search       = '';
    public int    $perPage      = 10;
    public string $sortField    = 'id';
    public string $sortDirection = 'asc';
    public bool   $confirmingDelete = false;
    public ?int   $barrioToDelete   = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->barrioToDelete   = $id;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        Barrio::find($this->barrioToDelete)?->delete();
        $this->confirmingDelete = false;
        $this->barrioToDelete   = null;
        session()->flash('message', 'Barrio eliminado correctamente.');
    }

    public function render()
    {
        $barrios = Barrio::query()
            ->where(function ($q) {
                $q->where('nombre',    'LIKE', "%{$this->search}%")
                    ->orWhere('sector',   'LIKE', "%{$this->search}%")
                    ->orWhere('parroquia', 'LIKE', "%{$this->search}%")
                    ->orWhere('id_DMQ',   'LIKE', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.barrios.index', compact('barrios'));
    }
}
