<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\ContratoServicio;
use App\Models\ContratoServicioDetalle;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.operacion')]
class Show extends Component
{
    public ContratoServicio $contrato;

    public bool $showDetalleModal = false;
    public ?int $detalleId = null;

    public $detalle_catalogo_servicio_id;
    public $detalle_cantidad;
    public $detalle_costo_unitario;

    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    protected function rulesDetalle(): array
    {
        return [
            'detalle_catalogo_servicio_id' => ['required', 'exists:catalogo_servicios,id'],
            'detalle_cantidad'             => ['required', 'integer', 'min:1'],
            'detalle_costo_unitario'       => ['required', 'numeric', 'min:0'],
        ];
    }

    public function mount(ContratoServicio $contrato)
    {
        $this->contrato = $contrato->load([
            'proveedor',
            'detalles.catalogoServicio',
            'formaPago.catalogoServicio',
        ]);

        // ... resto del método sin cambios
    }

    public function openEditDetalle(int $detalleId): void
    {
        if (!$this->contrato->puedeEditarServicios()) {
            session()->flash('error', 'No se pueden editar servicios: el contrato ya no está Pendiente.');
            return;
        }

        $detalle = $this->contrato->detalles()->findOrFail($detalleId);
        $this->detalleId = $detalle->id;
        $this->detalle_catalogo_servicio_id = $detalle->catalogo_servicio_id;
        $this->detalle_cantidad = $detalle->cantidad;
        $this->detalle_costo_unitario = $detalle->costo_unitario;
        $this->showDetalleModal = true;
    }

    public function saveDetalle(): void
    {
        if (!$this->contrato->puedeEditarServicios()) {
            $this->addError('global', 'No se pueden editar servicios: el contrato ya no está Pendiente.');
            return;
        }

        $data = $this->validate($this->rulesDetalle());

        $this->contrato->detalles()->updateOrCreate(
            ['id' => $this->detalleId],
            [
                'catalogo_servicio_id' => $data['detalle_catalogo_servicio_id'],
                'cantidad'             => $data['detalle_cantidad'],
                'costo_unitario'       => $data['detalle_costo_unitario'],
            ]
        );

        $this->showDetalleModal = false;
        $this->reset(['detalleId', 'detalle_catalogo_servicio_id', 'detalle_cantidad', 'detalle_costo_unitario']);

        session()->flash('message', 'Servicio guardado correctamente.');
    }

    public function confirmDelete(int $id): void
    {
        if (!$this->contrato->puedeEditarServicios()) {
            session()->flash('error', 'No se pueden eliminar servicios: el contrato ya no está Pendiente.');
            return;
        }

        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        if (!$this->contrato->puedeEditarServicios()) {
            session()->flash('error', 'No se pueden eliminar servicios: el contrato ya no está Pendiente.');
            $this->confirmingDelete = false;
            return;
        }

        $this->contrato->detalles()->findOrFail($this->deleteId)->delete();

        $this->confirmingDelete = false;
        $this->deleteId = null;

        session()->flash('message', 'Servicio eliminado correctamente.');
    }

    public function render()
    {
        return view('livewire.operacion.contratos-servicios.show', [
            'detalles' => $this->contrato->detalles()->with('catalogoServicio')->get(),
        ]);
    }
}
