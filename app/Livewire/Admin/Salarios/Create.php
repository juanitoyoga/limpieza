<?php

namespace App\Livewire\Admin\Salarios;

use Livewire\Component;
use App\Models\SalarioMinimo;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Create extends Component
{
    public $year;
    public $valor_usd;

    protected $messages = [
        'year.required' => 'El año es obligatorio.',
        'year.integer' => 'El año debe ser un número entero.',
        'year.digits' => 'El año debe tener exactamente 4 dígitos.',
        'year.min' => 'El año no puede ser menor a 1900.',
        'year.max' => 'El año no puede ser mayor al siguiente año.',
        'year.unique' => 'Ya existe un registro para este año.',

        'valor_usd.required' => 'El valor es obligatorio.',
        'valor_usd.numeric' => 'Debe ingresar un número válido.',
        'valor_usd.min' => 'El valor no puede ser negativo.',
        'valor_usd.max' => 'El valor excede el límite permitido.',
    ];

    protected function rules()
    {
        return [
            'year' => [
                'required',
                'integer',
                'digits:4',
                'min:1900',
                'max:' . (date('Y') + 1),
                'unique:salariominimo,year',
            ],
            'valor_usd' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
        ];
    }

    public function store()
    {
        $this->validate();

        SalarioMinimo::create([
            'year' => $this->year,
            'valor_usd' => $this->valor_usd,
        ]);

        session()->flash('message', 'Salario Minimo creado correctamente.');

        return redirect()->route('salarios.index');
    }

    public function render()
    {
        return view('livewire.admin.salarios.create');
    }
}
