<?php

namespace App\Livewire\Operacion\Ofertas;

use Livewire\Component;
use App\Models\{Oferta, Resolucion, ResolucionServicio, OfertaServicio};

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use App\Livewire\Concerns\ManejaEstadoBloqueado;

#[Layout('layouts.operacion')]

class Servicios extends Component
{
    use ManejaEstadoBloqueado;

    private const CAMPOS_EDITABLES = ['cantidad', 'costo_unitario'];

    public Oferta $oferta;
    public Resolucion $resolucion;

    public $serviciosResolucion = [];
    public $serviciosOferta = [];


    public function mount(Oferta $oferta)
    {

        $this->oferta = $oferta;
        $this->resolucion = Resolucion::find($oferta->resolucion_id);


        if (Gate::denies('ofertas.editarServicios', $oferta)) {
            $this->bloquearAcceso(
                mensaje: 'No tienes permiso para editar los servicios de esta oferta.',
                ruta: route('ofertas.show', $oferta),
            );
            return;
        }

        // Solo se puede tocar servicios mientras la oferta sigue Pendiente
        if ($oferta->auth_status !== Oferta::ESTADO_PENDIENTE) {
            $this->bloquearAcceso(
                mensaje: 'No se pueden modificar los servicios de una oferta ya verificada, aprobada o rechazada.',
                ruta: route('ofertas.show', $oferta),
                detalles: ['Estado actual' => $oferta->estadoLabel()],
            );
            return;
        }


        $this->actualizarListas();
    }


    /**
     * Vuelve a verificar el estado justo antes de cada acción, por si
     * la oferta cambió de estado mientras el usuario tenía la pantalla abierta
     * (ej. otro usuario ya la verificó en paralelo).
     */
    private function verificarEditable(): void
    {
        if ($this->oferta->fresh()->auth_status !== Oferta::ESTADO_PENDIENTE) {
            $this->bloquearAcceso(
                mensaje: 'La oferta cambió de estado y ya no se puede editar.',
                ruta: route('ofertas.show', $this->oferta),
            );
        }
    }

    public function agregarServicio(int $resolucionServicioId)
    {
        $this->verificarEditable();

        $res = ResolucionServicio::with('catalogoServicio')->findOrFail($resolucionServicioId);

        if ($res->resolucion_id !== $this->oferta->resolucion_id) {
            $this->bloquearAcceso(
                mensaje: 'Este servicio no pertenece a la resolución de esta oferta.',
                ruta: route('ofertas.show', $this->oferta),
                nivel: 'error',
            );
            return;
        }

        if (OfertaServicio::where('oferta_id', $this->oferta->id)
            ->where('catalogo_servicio_id', $res->catalogo_servicio_id)
            ->exists()
        ) {
            $this->dispatch('toast', message: 'Este servicio ya está agregado a la oferta.');
            return;
        }


        OfertaServicio::create([
            'oferta_id'              => $this->oferta->id,
            'catalogo_servicio_id'   => $res->catalogo_servicio_id,
            'resolucion_servicio_id' => $res->id,
            'cantidad'               => $res->cantidad ?? 1,
            'costo_unitario'         => $res->costo_unitario ?? 0,
            'subtotal'               => 0,
        ]);

        $this->actualizarListas();
    }



    public function actualizarServicio(int $id, string $campo, $valor)
    {
        $this->verificarEditable();

        abort_unless(in_array($campo, self::CAMPOS_EDITABLES, true), 403);

        $servicio = OfertaServicio::where('oferta_id', $this->oferta->id)
            ->with('resolucionServicio')
            ->findOrFail($id);

        if ($campo === 'cantidad') {
            $solicitado = $servicio->resolucionServicio?->cantidad;

            $validator = Validator::make(
                ['valor' => $valor],
                ['valor' => 'required|integer|min:1' . ($solicitado ? "|max:{$solicitado}" : '')],
                ['valor.max' => "La cantidad no puede superar lo solicitado en la resolución ({$solicitado})."]
            );
        } else {
            $validator = Validator::make(
                ['valor' => $valor],
                ['valor' => 'required|numeric|min:0']
            );
        }

        if ($validator->fails()) {
            $this->dispatch('toast', message: $validator->errors()->first('valor'));
            return;
        }

        try {
            $servicio->$campo = $valor;
            $servicio->save();
        } catch (\DomainException $e) {
            $this->dispatch('toast', message: $e->getMessage());
            return;
        }

        $this->actualizarListas();
    }

    public function eliminarServicio(int $id)
    {
        $this->verificarEditable();

        OfertaServicio::where('oferta_id', $this->oferta->id)->findOrFail($id)->delete();
        $this->actualizarListas();
    }


    private function actualizarListas()
    {
        $catalogosYaOfertados = $this->oferta->ofertaServicios()
            ->pluck('catalogo_servicio_id');

        // Servicios de la resolución que aún NO están en la oferta
        $this->serviciosResolucion = $this->resolucion->resolucionServicios()
            ->whereNotIn('catalogo_servicio_id', $catalogosYaOfertados)
            ->with('catalogoServicio')
            ->get();

        // Servicios ya agregados a la oferta
        $this->serviciosOferta = $this->oferta->ofertaServicios()
            ->with('catalogoServicio', 'resolucionServicio')
            ->get();
    }


    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.ofertas.servicios');
    }
}
