<?php

namespace App\Livewire\Admin\Barrios;

use Livewire\Component;

use App\Models\Barrio;

use Livewire\Attributes\Layout;


#[Layout('layouts.admin')]
class Create extends Component
{

    public $nombre;

    public $id_DMQ;

    public $sector;
    public $parroquia;

    protected $rules = [
        'nombre' => 'required|string|min:3|max:100',
        'id_DMQ' => 'required|string|max:50',
        'sector' => 'required|string|max:100',
        'parroquia' => 'required|string|max:255',
    ];

    public function store()
    {
        $this->validate();

        Barrio::create([
            'nombre' => $this->nombre,
            'id_DMQ' => $this->id_DMQ,
            'sector' => $this->sector,
            'parroquia' => $this->parroquia,
        ]);

        session()->flash('message', 'Barrio creado correctamente.');

        // Redirigir al listado
        return redirect()->route('barrios.index');
    }

    public function render()
    {
        return view('livewire.admin.barrios.create');
    }
}
