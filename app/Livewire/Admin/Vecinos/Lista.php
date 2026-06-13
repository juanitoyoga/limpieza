<?php

namespace App\Livewire\Admin\Vecinos;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Lista extends Component
{
    use WithPagination;

    // Filtros
    public string $cedula     = '';
    public string $nombre     = '';
    public string $email      = '';
    public string $id_DMQ     = '';
    public string $barrio     = '';
    public string $parroquia  = '';
    public string $activo     = '';

    // Tabla
    public string $sortField     = 'last_name';
    public string $sortDirection = 'asc';
    public int    $perPage       = 10;

    // Modal eliminación (si lo necesitas luego)
    public bool $confirmingDelete = false;
    public ?int $deleteId         = null;

    public function mount()
    {
        $this->cedula    = request('cedula', '');
        $this->nombre    = request('nombre', '');
        $this->email     = request('email', '');
        $this->id_DMQ    = request('id_DMQ', '');
        $this->barrio    = request('barrio', '');
        $this->parroquia = request('parroquia', '');
        $this->activo    = request('activo', '');
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

    public function render()
    {
        $usuarios = User::query()
            ->whereHas('vecino') // Solo usuarios con vecino
            ->with(['vecino', 'vecino.barrio'])

            // Filtros sobre Vecino
            ->when(
                $this->cedula,
                fn($q) =>
                $q->whereHas(
                    'vecino',
                    fn($v) =>
                    $v->where('cedula', 'like', "%{$this->cedula}%")
                )
            )

            // Filtro nombre (User)
            ->when(
                $this->nombre,
                fn($q) =>
                $q->where(function ($sub) {
                    $sub->where('first_name', 'like', "%{$this->nombre}%")
                        ->orWhere('last_name', 'like', "%{$this->nombre}%");
                })
            )

            // Filtro email (User)
            ->when(
                $this->email,
                fn($q) =>
                $q->where('email', 'like', "%{$this->email}%")
            )

            // Filtro id_DMQ (Barrio)
            ->when(
                $this->id_DMQ,
                fn($q) =>
                $q->whereHas(
                    'vecino.barrio',
                    fn($b) =>
                    $b->where('id_DMQ', 'like', "%{$this->id_DMQ}%")
                )
            )

            // Filtro nombre del barrio
            ->when(
                $this->barrio,
                fn($q) =>
                $q->whereHas(
                    'vecino.barrio',
                    fn($b) =>
                    $b->where('nombre', 'like', "%{$this->barrio}%")
                )
            )

            // Filtro parroquia
            ->when(
                $this->parroquia,
                fn($q) =>
                $q->whereHas(
                    'vecino.barrio',
                    fn($b) =>
                    $b->where('parroquia', 'like', "%{$this->parroquia}%")
                )
            )

            // Filtro activo/inactivo del vecino
            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->whereHas(
                    'vecino',
                    fn($v) =>
                    $v->where('is_active', (bool)$this->activo)
                )
            )

            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.vecinos.lista', compact('usuarios'));
    }
}
