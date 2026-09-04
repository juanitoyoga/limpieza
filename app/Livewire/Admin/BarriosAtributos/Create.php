<?php

namespace App\Livewire\Admin\BarriosAtributos;

use Livewire\Component;
use Livewire\Attributes\Layout;

use App\Models\Barrio;
use App\Models\Ordenanza332;
use App\Models\BarrioAtributo;

#[Layout('layouts.admin')]
class Create extends Component
{
    public $barrio_id = '';
    public $ordenanza332_id = '';
    public $plazo_horas = '';
    public $nro_convenio = '';

    public function save()
    {
        $this->validate([
            'barrio_id' => 'required|exists:barrios,id',
            'ordenanza332_id' => 'required|exists:ordenanza332,id',
            'plazo_horas' => 'required|integer|min:0',
            'nro_convenio' => 'nullable|integer|min:0',
        ]);

        BarrioAtributo::create([
            'barrio_id' => $this->barrio_id,
            'ordenanza332_id' => $this->ordenanza332_id,
            'plazo_horas' => $this->plazo_horas,
            'nro_convenio' => $this->nro_convenio,
        ]);

        session()->flash('success', 'Registro creado correctamente.');

        return redirect()->route('barriosatributos.index');
    }

    public function render()
    {
        return view('livewire.admin.barriosatributos.create', [
            'barrios' => Barrio::orderBy('nombre')->get(),
            'ordenanzas' => Ordenanza332::orderBy('nombre')->get(),
        ]);
    }
}
