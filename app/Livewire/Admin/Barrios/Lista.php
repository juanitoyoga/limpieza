<?php

namespace App\Livewire\Admin\Barrios;

use App\Models\Barrio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Lista extends Component
{
    use WithPagination;

    // Filtros recibidos por URL
    public string $id_DMQ    = '';
    public string $nombre    = '';
    public string $sector    = '';
    public string $parroquia = '';
    public string $activo    = '';

    // Tabla
    public string $sortField     = 'nombre';
    public string $sortDirection = 'asc';
    public int    $perPage       = 10;

    // Modal eliminación
    public bool $confirmingDelete = false;
    public ?int $deleteId         = null;

    // Cargar filtros desde query string al montar
    public function mount()
    {
        $this->id_DMQ    = request('id_DMQ',    '');
        $this->nombre    = request('nombre',    '');
        $this->sector    = request('sector',    '');
        $this->parroquia = request('parroquia', '');
        $this->activo    = request('activo',    '');
    }

    public function sortBy(string $field): void
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId         = $id;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        Barrio::findOrFail($this->deleteId)->delete();
        $this->confirmingDelete = false;
        $this->deleteId         = null;
        session()->flash('message', 'Barrio eliminado correctamente.');
    }

    public function toggle(int $id): void
    {
        $barrio = Barrio::findOrFail($id);
        $barrio->update(['activo' => !$barrio->activo]);
        session()->flash(
            'message',
            'Barrio ' . ($barrio->activo ? 'activado' : 'desactivado') . ' correctamente.'
        );
    }

    public function render()
    {
        $barrios = Barrio::query()
            ->when($this->id_DMQ,    fn($q) => $q->where('id_DMQ',    'like', "%{$this->id_DMQ}%"))
            ->when($this->nombre,    fn($q) => $q->where('nombre',    'like', "%{$this->nombre}%"))
            ->when($this->sector,    fn($q) => $q->where('sector',    'like', "%{$this->sector}%"))
            ->when($this->parroquia, fn($q) => $q->where('parroquia', 'like', "%{$this->parroquia}%"))
            ->when($this->activo !== '', fn($q) => $q->where('activo', (bool)$this->activo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.barrios.lista', compact('barrios'));
    }
}
