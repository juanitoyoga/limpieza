<?php

namespace App\Livewire\Admin\Contratos;

use App\Models\Barrio;
use App\Models\Contrato;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]

class Edit extends Component
{
    use WithFileUploads;

    public $barrio_id;
    public $numero_contrato;
    public $fecha_inicio;
    public $fecha_fin;
    public $monto_total;
    public $porcentaje_barrio;
    public $porcentaje_dmq;
    public $porcentaje_ltr;
    public $archivo;

    protected $rules = [
        'barrio_id'         => 'required|exists:barrios,id',
        'numero_contrato'   => 'required|string|max:255',
        'fecha_inicio'      => 'required|date',
        'fecha_fin'         => 'required|date|after_or_equal:fecha_inicio',
        'monto_total'       => 'required|numeric|min:0',
        'porcentaje_barrio' => 'required|numeric|min:0|max:100',
        'porcentaje_dmq'    => 'required|numeric|min:0|max:100',
        'porcentaje_ltr'    => 'required|numeric|min:0|max:100',
        'archivo'           => 'nullable|file|mimes:pdf|max:4096',
    ];

    public function save()
    {
        $this->validate();

        $path = null;
        $hash = null;

        if ($this->archivo) {
            $path = $this->archivo->store('contratos');
            $hash = hash_file('sha256', storage_path('app/' . $path));
        }

        $contrato = Contrato::create([
            'barrio_id'         => $this->barrio_id,
            'numero_contrato'   => $this->numero_contrato,
            'fecha_inicio'      => $this->fecha_inicio,
            'fecha_fin'         => $this->fecha_fin,
            'monto_total'       => $this->monto_total,
            'porcentaje_barrio' => $this->porcentaje_barrio,
            'porcentaje_dmq'    => $this->porcentaje_dmq,
            'porcentaje_ltr'    => $this->porcentaje_ltr,
            'contrato_path'     => $path,
            'document_hash'     => $hash,
            'estado'            => Contrato::ESTADO_PENDIENTE,
        ]);

        session()->flash('message', 'Contrato modificado correctamente.');

        return redirect()->route('contratos.edit');
    }

    public function render()
    {
        return view('livewire.admin.contratos.edit', [
            'barrios' => Barrio::orderBy('nombre')->get(),
        ]);
    }
}
