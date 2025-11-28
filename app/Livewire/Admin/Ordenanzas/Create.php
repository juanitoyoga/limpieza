<?php

namespace App\Livewire\Admin\Ordenanzas;

use Livewire\Component;

use App\Models\Ordenanza332;

use Livewire\Attributes\Layout;


#[Layout('layouts.admin')]
class Create extends Component
{

    public $codigo;

    public $tipo;

    public $descripcion;
    public $nivel_gravedad;

    protected $rules = [
        'codigo' => 'required|string|min:3|max:100',
        'tipo' => 'required|string|max:50',
        'descripcion' => 'required|string|max:500',
        'nivel_gravedad' => 'required|string|max:255',
    ];

    public function store()
    {
        $this->validate();

        Ordenanza332::create([
            'codigo' => $this->codigo,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'nivel_gravedad' => $this->nivel_gravedad,
        ]);

        session()->flash('message', 'Contravencion creada correctamente.');

        // Redirigir al listado
        return redirect()->route('ordenanzas.index');
    }

    public function render()
    {
        return view('livewire.admin.ordenanzas.create');
    }
}
