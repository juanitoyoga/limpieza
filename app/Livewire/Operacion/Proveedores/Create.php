<?php

namespace App\Livewire\Operacion\Proveedores;

use App\Models\Proveedor;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Create extends Component
{
    public $razon_social;
    public $ruc;
    public $representante_legal;
    public $tipo_servicio;
    public $email;
    public $telefono;
    public $direccion;
    public $cuenta_bancaria;
    public $banco;
    public $estado = 'activo';

    protected $rules = [
        'razon_social'         => 'required|string|max:255',
        'ruc'                  => 'required|string|size:13|unique:proveedores,ruc',
        'representante_legal'  => 'nullable|string|max:255',
        'tipo_servicio'        => 'nullable|string|max:255',
        'email'                => 'nullable|email|max:255',
        'telefono'             => 'nullable|string|max:20',
        'direccion'            => 'nullable|string|max:255',
        'cuenta_bancaria'      => 'nullable|string|max:50',
        'banco'                => 'nullable|string|max:255',
        'estado'               => 'required|in:activo,inactivo',
    ];

    public function save()
    {
        $this->validate();

        Proveedor::create([
            'razon_social'        => $this->razon_social,
            'ruc'                 => $this->ruc,
            'representante_legal' => $this->representante_legal,
            'tipo_servicio'       => $this->tipo_servicio,
            'email'               => $this->email,
            'telefono'            => $this->telefono,
            'direccion'           => $this->direccion,
            'cuenta_bancaria'     => $this->cuenta_bancaria,
            'banco'               => $this->banco,
            'estado'              => $this->estado,
        ]);

        session()->flash('message', 'Proveedor creado correctamente.');

        return redirect()->route('proveedores.lista');
    }

    public function render()
    {
        return view('livewire.operacion.proveedores.create');
    }
}
