<?php

namespace App\Livewire\Admin\BarriosAtributos;

use App\Models\BarrioAtributo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Lista extends Component
{
    use WithPagination;

    // Filtros
    public string $barrio_id = '';
    public string $ordenanza332_id = '';

    // Tabla
    public string $sortField = 'barrio_id';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    // Modal eliminación
    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    // Sincronizar filtros con la URL
    protected $updatesQueryString = [
        'barrio_id',
        'ordenanza332_id',
        'sortField',
        'sortDirection',
        'perPage',
    ];

    public function mount(): void
    {
        $this->barrio_id = request('barrio_id', '');
        $this->ordenanza332_id = request('ordenanza332_id', '');
    }

    public function updatingBarrioId(): void
    {
        $this->resetPage();
    }

    public function updatingOrdenanza332Id(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';

        $this->sortField = $field;
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        BarrioAtributo::findOrFail($this->deleteId)->delete();

        $this->confirmingDelete = false;
        $this->deleteId = null;

        session()->flash('success', 'Registro eliminado correctamente.');
    }

    public function render()
    {
        $registros = BarrioAtributo::query()
            ->with(['barrio', 'ordenanza'])
            ->when($this->barrio_id, fn($q) => $q->where('barrio_id', $this->barrio_id))
            ->when($this->ordenanza332_id, fn($q) => $q->where('ordenanza332_id', $this->ordenanza332_id))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.barriosAtributos.lista', [
            'registros' => $registros,
        ]);
    }
}
