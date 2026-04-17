<?php

namespace App\Livewire\Admin\Porcentajes;

use Livewire\Component;
use App\Models\Ordenanza332;
use App\Models\SalarioMinimo;
use App\Models\PorcentajeMultas;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Create extends Component

{
    public $ordenanza_id;
    public $salario_id;
    public $porcentaje;

    public $ordenanzas = [];
    public $salarios = [];

    public function mount()
    {
        $this->ordenanzas = Ordenanza332::orderBy('codigo')->get();
        $this->salarios = SalarioMinimo::orderBy('year', 'desc')->get();
    }

    protected function rules()
    {
        return [
            'ordenanza_id' => 'required|exists:ordenanza332,id',
            'salario_id'   => 'required|exists:salariominimo,id',
            'porcentaje'   => 'required|numeric|min:0|max:100',
        ];
    }

    public function store()
    {
        $this->validate();

        PorcentajeMultas::create([
            'ordenanza332_id' => $this->ordenanza_id,
            'salariominimo_id'   => $this->salario_id,
            'porcentaje'   => $this->porcentaje,
        ]);

        session()->flash('success', 'Porcentaje de multa registrado correctamente.');

        return redirect()->route('porcentajes.index');
    }

    public function render()
    {
        return view('livewire.admin.porcentajes.create');
    }
}
