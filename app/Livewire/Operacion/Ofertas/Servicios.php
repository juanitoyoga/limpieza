<?php

namespace App\Livewire\Operacion\Ofertas;

use Livewire\Component;
use App\Models\Oferta;
use App\Models\OfertaServicio;
use App\Models\ResolucionServicio;
use Illuminate\Support\Facades\DB;

class Servicios extends Component
{
    public Oferta $oferta;

    public $serviciosResolucion = [];
    public $serviciosOferta = [];

    public function mount(Oferta $oferta)
    {
        $this->oferta = $oferta;

        // Servicios definidos en la resolución (orden de compra del barrio)
        $this->serviciosResolucion = ResolucionServicio::where('resolucion_id', $oferta->resolucion_id)
            ->with('catalogoServicio')
            ->orderBy('catalogo_servicio_id')
            ->get();

        // Servicios ya agregados a la oferta
        $this->serviciosOferta = OfertaServicio::where('oferta_id', $oferta->id)
            ->with(['catalogoServicio', 'resolucionServicio'])
            ->orderBy('catalogo_servicio_id')
            ->get();
    }

    public function agregarServicio(int $resolucionServicioId)
    {
        $res = ResolucionServicio::with('catalogoServicio')->findOrFail($resolucionServicioId);

        // Evitar duplicados
        if (OfertaServicio::where('oferta_id', $this->oferta->id)
            ->where('catalogo_servicio_id', $res->catalogo_servicio_id)
            ->exists()
        ) {
            $this->dispatch('toast', message: 'Este servicio ya está agregado a la oferta.');
            return;
        }

        OfertaServicio::create([
            'oferta_id'             => $this->oferta->id,
            'catalogo_servicio_id'  => $res->catalogo_servicio_id,
            'resolucion_servicio_id' => $res->id,
            'cantidad'              => $res->cantidad ?? 1,
            'costo_unitario'        => $res->costo_unitario ?? 0,
            'subtotal'              => 0,
        ]);

        $this->actualizarListas();
    }

    public function actualizarServicio(int $id, string $campo, $valor)
    {
        $servicio = OfertaServicio::findOrFail($id);
        $servicio->$campo = $valor;
        $servicio->save();

        $this->actualizarListas();
    }

    public function eliminarServicio(int $id)
    {
        OfertaServicio::findOrFail($id)->delete();
        $this->actualizarListas();
    }

    private function actualizarListas()
    {
        $this->serviciosOferta = OfertaServicio::where('oferta_id', $this->oferta->id)
            ->with(['catalogoServicio', 'resolucionServicio'])
            ->orderBy('catalogo_servicio_id')
            ->get();

        $this->oferta->refresh();
    }

    public function render()
    {
        return view('livewire.operacion.ofertas.servicios');
    }
}
