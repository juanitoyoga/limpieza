<?php

namespace App\Livewire\Operacion\Ofertas;

use App\Models\{Oferta, AuditEvent};
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\{Auth, Gate};

#[Layout('layouts.operacion')]
class Show extends Component
{
    public Oferta $oferta;

    public function mount(Oferta $oferta)
    {
        Gate::authorize('ofertas.verDetalle', $oferta);

        $this->oferta = $oferta->load([
            'proveedor',
            'resolucion',
            'ofertaServicios.catalogoServicio',
            'verificador',
            'aprobador',
            'rechazador',
        ]);
    }

    public function puedeVerDocumento(): bool
    {
        return $this->oferta->documento_original_path
            && Gate::allows('ofertas.verificar', $this->oferta);
    }

    public function puedeEditarServicios(): bool
    {
        return $this->oferta->auth_status === Oferta::ESTADO_PENDIENTE
            && Gate::allows('ofertas.editarServicios', $this->oferta);
    }

    public function puedeVerificar(): bool
    {
        return $this->oferta->auth_status === Oferta::ESTADO_PENDIENTE
            && Gate::allows('ofertas.verificar', $this->oferta);
    }

    public function puedeAprobar(): bool
    {
        return $this->oferta->auth_status === Oferta::ESTADO_VERIFICADA
            && Gate::allows('ofertas.aprobar', $this->oferta);
    }

    public function puedeRechazar(): bool
    {
        return in_array($this->oferta->auth_status, [Oferta::ESTADO_PENDIENTE, Oferta::ESTADO_VERIFICADA], true)
            && Gate::allows('ofertas.rechazar', $this->oferta);
    }

    public function render()
    {
        return view('livewire.operacion.ofertas.show', [
            'historial' => AuditEvent::forModel($this->oferta)
                ->orderByDesc('event_at')
                ->get(),
        ]);
    }
}
