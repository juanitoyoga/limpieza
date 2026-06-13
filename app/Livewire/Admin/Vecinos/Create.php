<?php

namespace App\Livewire\Admin\Vecinos;

use App\Models\Vecino;
use App\Models\Barrio;
use App\Models\Catalogo;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Create extends Component
{
    public string $id_DMQ = '';
    public string $calle_principal = '';
    public string $numero = '';
    public string $calle_secundaria = '';
    public string $referencias = '';
    public string $telefono = '';

    public $barrios = [];
    public array $ocupaciones = [];
    public array $deportes = [];
    public array $recreaciones = [];

    public $catalogoOcupaciones = [];
    public $catalogoDeportes = [];
    public $catalogoRecreaciones = [];

    public function mount()
    {
        // Si ya es vecino, no puede volver a registrarse
        // El signo '?' evita que el sistema explote si no hay sesión iniciada
        if (auth()->user()?->role_name === 'Vecino') {
            return redirect()->route('vecinos.index')
                ->with('message', 'Ya estás registrado como vecino.');
        }

        $this->barrios = Barrio::orderBy('nombre')->get();
        // Cargar catálogos
        $this->catalogoOcupaciones = Catalogo::where('tipo', 'ocupacion')->orderBy('nombre')->get();
        $this->catalogoDeportes    = Catalogo::where('tipo', 'deporte')->orderBy('nombre')->get();
        $this->catalogoRecreaciones = Catalogo::where('tipo', 'recreacion')->orderBy('nombre')->get();
    }

    public function save()
    {
        $this->validate([
            'id_DMQ'           => 'required|exists:barrios,id_DMQ',
            'calle_principal'  => 'required|string|max:255',
            'numero'           => 'required|string|max:50',
            'calle_secundaria' => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:20',
            'referencias'      => 'nullable|string|max:500',
            // Nuevos campos
            'ocupaciones'      => 'array',
            'deportes'         => 'array',
            'recreaciones'     => 'array',
        ]);

        Vecino::create([
            'user_id'         => auth()->id(),
            'id_DMQ'          => $this->id_DMQ,
            'cedula'          => auth()->user()->nro_id,
            'telefono'        => $this->telefono,
            'calle_principal' => $this->calle_principal,
            'numero'          => $this->numero,
            'calle_secundaria' => $this->calle_secundaria,
            'referencias'     => $this->referencias,
            'fecha_registro'  => now(),
            'is_active'       => true,
            // Guardar JSON
            'ocupacion'       => $this->ocupaciones,
            'deportes'        => $this->deportes,
            'recreacion'      => $this->recreaciones,
        ]);

        auth()->user()->update([
            'role_name' => 'Vecino'
        ]);

        return redirect()->route('vecinos.lista')
            ->with('message', 'Registro de vecino creado correctamente.');
    }

    public function render()
    {
        return view('livewire.admin.vecinos.create');
    }
}
