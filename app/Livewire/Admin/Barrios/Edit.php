<?php

namespace App\Livewire\Admin\Barrios;

use Livewire\Component;

use App\Models\Barrio;

use Livewire\Attributes\Layout;



#[Layout('layouts.admin')]
class Edit extends Component
{
    public $barrio;
    public $nombre;
    public $sector;
    public $parroquia;

    public $id_DMQ;
    protected $rules = [
        'nombre' => 'required|string|min:3|max:100',
        'id_DMQ' => 'nullable|string|max:50',
        'sector' => 'nullable|string|max:100',
        'parroquia' => 'nullable|string|max:255',
    ];

    // Laravel inyecta el modelo con Route Model Binding
    public function mount(Barrio $barrio)
    {
        $this->barrio = $barrio;
        $this->nombre = $barrio->nombre;
        $this->sector = $barrio->sector;
        $this->parroquia = $barrio->parroquia;
        $this->id_DMQ = $barrio->id_DMQ;
    }

    public function update()
    {
        $this->validate();

        $this->barrio->update([
            'nombre' => $this->nombre,
            'sector' => $this->sector,
            'parroquia' => $this->parroquia,
            'id_DMQ' => $this->id_DMQ,
        ]);

        session()->flash('message', 'Barrio actualizado correctamente.');

        return redirect()->route('barrios.index');
    }

    public function render()
    {
        return view('livewire.admin.barrios.edit');
    }
}
