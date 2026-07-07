<?php

namespace App\Livewire\Operacion\Nominations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Nomination;
use App\Models\User;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Index extends Component
{
    use WithPagination;

    // ─── Filtros y búsqueda ───────────────────────────────────────────────
    public $search = '';
    public $estado = '';
    public $rol = '';
    public $released_by = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $nominator_id = '';
    public $candidate_user_id = '';
    public $issuer_type = '';
    public $verified_by = '';
    public $rejected_by = '';

    public $fecha_emision_inicio = '';
    public $fecha_emision_fin = '';

    public $vigencia_inicio = '';
    public $vigencia_fin = '';

    public $verified_at_inicio = '';
    public $verified_at_fin = '';

    public $rejected_at_inicio = '';
    public $rejected_at_fin = '';


    // ─── Tabla ─────────────────────────────────────────────────────────────
    public $perPage = 10;
    public $sortField = 'fecha_emision';
    public $sortDirection = 'desc';

    // ─── Anulación ─────────────────────────────────────────────────────────
    public $confirmingAnulacion = false;
    public $nominationToAnular = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($property)
    {
        if (!in_array($property, ['sortField', 'sortDirection', 'perPage'])) {
            $this->resetPage();
        }
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

    // ─── Filtros avanzados ────────────────────────────────────────────────
    public function buscar()
    {
        $this->resetPage();
    }

    public function limpiar()
    {
        $this->reset([
            'search',
            'estado',
            'rol',
            'released_by',
            'fecha_inicio',
            'fecha_fin',
        ]);

        $this->resetPage();
    }

    // ─── Anulación ─────────────────────────────────────────────────────────
    public function confirmAnular($id)
    {
        $this->nominationToAnular = $id;
        $this->confirmingAnulacion = true;
    }

    public function anular()
    {
        $nomination = Nomination::find($this->nominationToAnular);

        if ($nomination && $nomination->estado !== Nomination::ESTADO_ANULADA) {
            $nomination->update(['estado' => Nomination::ESTADO_ANULADA]);
        }

        $this->confirmingAnulacion = false;
        $this->nominationToAnular = null;

        session()->flash('success', 'Trámite anulado correctamente.');
    }

    private function buildQuery()
    {
        return Nomination::query()
            ->with(['nominator', 'candidate', 'verifier', 'approver'])

            // Búsqueda general
            ->when($this->search, function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('numero_tramite', 'like', "%{$search}%")
                        ->orWhere('role_name', 'like', "%{$search}%")
                        ->orWhere('issuer_type', 'like', "%{$search}%")
                        ->orWhere('released_by', 'like', "%{$search}%")
                        ->orWhereDate('fecha_emision', $search)
                        ->orWhereHas('candidate', function ($u) use ($search) {
                            $u->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('nominator', function ($u) use ($search) {
                            $u->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })

            // Filtros avanzados
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->when($this->rol, fn($q) => $q->where('role_name', 'like', "%{$this->rol}%"))
            ->when($this->issuer_type, fn($q) => $q->where('issuer_type', $this->issuer_type))

            ->when($this->nominator_id, fn($q) => $q->where('nominator_id', $this->nominator_id))
            ->when($this->candidate_user_id, fn($q) => $q->where('candidate_user_id', $this->candidate_user_id))

            ->when($this->verified_by, fn($q) => $q->where('verified_by', $this->verified_by))
            ->when($this->rejected_by, fn($q) => $q->where('rejected_by', $this->rejected_by))

            // Fechas
            ->when(
                $this->fecha_emision_inicio,
                fn($q) =>
                $q->whereDate('fecha_emision', '>=', $this->fecha_emision_inicio)
            )
            ->when(
                $this->fecha_emision_fin,
                fn($q) =>
                $q->whereDate('fecha_emision', '<=', $this->fecha_emision_fin)
            )

            ->when(
                $this->vigencia_inicio,
                fn($q) =>
                $q->whereDate('fecha_inicio_vigencia', '>=', $this->vigencia_inicio)
            )
            ->when(
                $this->vigencia_fin,
                fn($q) =>
                $q->whereDate('fecha_fin_vigencia', '<=', $this->vigencia_fin)
            )

            ->when(
                $this->verified_at_inicio,
                fn($q) =>
                $q->whereDate('verified_at', '>=', $this->verified_at_inicio)
            )
            ->when(
                $this->verified_at_fin,
                fn($q) =>
                $q->whereDate('verified_at', '<=', $this->verified_at_fin)
            )

            ->when(
                $this->rejected_at_inicio,
                fn($q) =>
                $q->whereDate('rejected_at', '>=', $this->rejected_at_inicio)
            )
            ->when(
                $this->rejected_at_fin,
                fn($q) =>
                $q->whereDate('rejected_at', '<=', $this->rejected_at_fin)
            )

            ->orderBy($this->sortField, $this->sortDirection);
    }


    public function render()
    {
        return view('livewire.operacion.nominations.index', [
            'nominations' => $this->buildQuery()->paginate($this->perPage),
            'users'       => User::orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }
}
