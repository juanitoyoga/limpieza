<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\Resolucion;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Concerns\ManejaEstadoBloqueado;

#[Layout('layouts.operacion')]
class Edit extends Component
{

    use ManejaEstadoBloqueado;

    public Resolucion $resolucion;

    public $codigo;
    public $barrio_id;
    public $titulo;
    public $descripcion;
    public $service_type_id;
    public $fecha_resolucion;
    public $numero_firmas;

    public function mount(Resolucion $resolucion)
    {
        Gate::authorize('resoluciones.edit', $resolucion);

        $this->resolucion = $resolucion;

        $this->codigo           = $resolucion->codigo;
        $this->barrio_id        = $resolucion->barrio_id;
        $this->titulo           = $resolucion->titulo;
        $this->descripcion      = $resolucion->descripcion;
        $this->service_type_id = $resolucion->service_type_id;
        $this->fecha_resolucion = $resolucion->fecha_resolucion?->format('Y-m-d');
        $this->numero_firmas    = $resolucion->numero_firmas;
    }

    protected function rules(): array
    {
        return [
            'codigo'           => 'required|string|max:255|unique:resoluciones,codigo,' . $this->resolucion->id,
            'barrio_id'        => 'required|exists:barrios,id',
            'titulo'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'service_type_id' => 'required|exists:service_types,id',
            'fecha_resolucion' => 'required|date',
            'numero_firmas'    => 'nullable|integer',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->resolucion->update([
            'codigo'           => $this->codigo,
            'barrio_id'        => $this->barrio_id,
            'titulo'           => $this->titulo,
            'descripcion'      => $this->descripcion,
            'service_type_id' => $this->service_type_id,
            'fecha_resolucion' => $this->fecha_resolucion,
            'numero_firmas'    => $this->numero_firmas,
        ]);

        session()->flash('message', 'Resolución actualizada correctamente.');

        return redirect()->route('resoluciones.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.resoluciones.edit');
    }
}
