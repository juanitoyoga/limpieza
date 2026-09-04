<?php

namespace App\Livewire\Operacion\Proveedores;

use App\Models\Proveedor;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Edit extends Component
{
    public Proveedor $proveedor;

    public $razon_social;
    public $ruc;
    public $representante_legal;
    public $tipo_servicio;
    public $email;
    public $telefono;
    public $direccion;
    public $cuenta_bancaria;
    public $banco;
    public $estado;

    public function mount(Proveedor $proveedor)
    {
        $this->proveedor = $proveedor;

        $this->razon_social        = $proveedor->razon_social;
        $this->ruc                 = $proveedor->ruc;
        $this->representante_legal = $proveedor->representante_legal;
        $this->tipo_servicio       = $proveedor->tipo_servicio;
        $this->email               = $proveedor->email;
        $this->telefono            = $proveedor->telefono;
        $this->direccion           = $proveedor->direccion;
        $this->cuenta_bancaria     = $proveedor->cuenta_bancaria;
        $this->banco               = $proveedor->banco;
        $this->estado              = $proveedor->estado;
    }

    protected function rules(): array
    {
        return [
            'razon_social'         => 'required|string|max:255',
            'ruc'                  => 'required|string|size:13|unique:proveedores,ruc,' . $this->proveedor->id,
            'representante_legal'  => 'nullable|string|max:255',
            'tipo_servicio'        => 'nullable|string|max:255',
            'email'                => 'nullable|email|max:255',
            'telefono'             => 'nullable|string|max:20',
            'direccion'            => 'nullable|string|max:255',
            'cuenta_bancaria'      => 'nullable|string|max:50',
            'banco'                => 'nullable|string|max:255',
            'estado'               => 'required|in:activo,inactivo',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->proveedor->update([
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

        session()->flash('message', 'Proveedor actualizado correctamente.');

        return redirect()->route('proveedores.lista');
    }

    public function render()
    {
        return view('livewire.operacion.proveedores.edit');
    }
}
