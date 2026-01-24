<?php

namespace App\Livewire\Operacion\Nominations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Nomination;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Ordenamiento
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $confirmingAnulacion = false;
    public $nominationToAnular = null;

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

    public function confirmAnular($id)
    {
        $this->nominationToAnular = $id;
        $this->confirmingAnulacion = true;
    }

    public function anular()
    {
        $nomination = Nomination::find($this->nominationToAnular);

        if ($nomination) {
            $nomination->update(['estado' => 'anulado']);
        }

        $this->confirmingAnulacion = false;
        $this->nominationToAnular = null;

        session()->flash('success', 'Trámite anulado correctamente.');
    }

    public function renderOLD()
    {
        $nominations = Nomination::with(['nominator','candidate'])
        ->when($this->search, function ($query) {
            $query->whereHas('nominator', function ($q) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
            })
            ->orWhereHas('candidate', function ($q) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
            })
            ->orWhere('status', 'LIKE', "%{$this->search}%")
            ->orWhere('numero_tramite', 'LIKE', "%{$this->search}%");
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

        return view('livewire.operacion.nominations.index', compact('nominations'));
    
    }

    public function render()
    {
        $nominations = Nomination::query()
            ->with('candidate')
            ->when($this->search, function ($query) {
                $search = trim($this->search);
    
                $query->where(function ($q) use ($search) {
    
                    // 🔎 Número de trámite
                    $q->where('numero_tramite', 'like', "%{$search}%")
    
                      // 🔎 Rol
                      ->orWhere('role_name', 'like', "%{$search}%")
    
                      // 🔎 Liberado por
                      ->orWhere('released_by', 'like', "%{$search}%")
    
                      // 🔎 Fecha de emisión (texto o fecha)
                      ->orWhereDate('fecha_emision', $search)
    
                      // 🔎 Nombre del candidato
                      ->orWhereHas('candidate', function ($u) use ($search) {
                          $u->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                      });
    
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    
        return view('livewire.operacion.nominations.index', compact('nominations'));
    }
    
}
