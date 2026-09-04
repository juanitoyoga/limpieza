<?php

namespace App\Livewire\Operacion\LogSistema;

use App\Models\LogSistema;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, On};
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.operacion')]
class Lista extends Component
{
    use WithPagination;

    public array $filtros = [];
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Selección para borrado masivo
    public array $seleccionados = [];
    public bool $seleccionarTodos = false;

    // Modal de detalle
    public ?int $detalleId = null;
    public bool $showDetalleModal = false;

    // Confirmación de borrado
    public bool $confirmingDelete = false;

    public function mount()
    {
        Gate::authorize('logs-sistema.ver');
    }

    #[On('filtros-actualizados')]
    public function actualizarFiltros(array $filtros): void
    {
        $this->filtros = $filtros;
        $this->seleccionados = [];
        $this->seleccionarTodos = false;
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function updatedSeleccionarTodos($value): void
    {
        $this->seleccionados = $value
            ? $this->logsQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function verDetalle(int $id): void
    {
        Gate::authorize('logs-sistema.ver');
        $this->detalleId = $id;
        $this->showDetalleModal = true;
    }

    public function confirmarBorradoMasivo(): void
    {
        if (empty($this->seleccionados)) {
            return;
        }
        $this->confirmingDelete = true;
    }

    public function borrarSeleccionados(): void
    {
        Gate::authorize('logs-sistema.eliminar');

        LogSistema::whereIn('id', $this->seleccionados)->delete();

        session()->flash('message', count($this->seleccionados) . ' registro(s) eliminado(s).');

        $this->seleccionados = [];
        $this->seleccionarTodos = false;
        $this->confirmingDelete = false;
    }

    private function logsQuery()
    {
        return LogSistema::query()
            ->when($this->filtros['search'] ?? null, function ($q, $search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('comentario', 'like', "%{$search}%")
                        ->orWhere('origen', 'like', "%{$search}%")
                        ->orWhere('mensaje_error', 'like', "%{$search}%");
                });
            })
            ->when($this->filtros['nivel'] ?? null, fn($q, $nivel) => $q->where('nivel', $nivel))
            ->when($this->filtros['tipoOrigen'] ?? null, fn($q, $tipo) => $q->where('tipo_origen', $tipo))
            ->when($this->filtros['fechaDesde'] ?? null, fn($q, $f) => $q->whereDate('created_at', '>=', $f))
            ->when($this->filtros['fechaHasta'] ?? null, fn($q, $f) => $q->whereDate('created_at', '<=', $f))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.operacion.log-sistema.lista', [
            'logs' => $this->logsQuery()->paginate(20),
            'detalle' => $this->detalleId ? LogSistema::with('usuario')->find($this->detalleId) : null,
        ]);
    }
}
