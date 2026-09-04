<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Livewire\Concerns\ManejaEstadoBloqueado;
use App\Models\ContratoServicio;
use App\Models\Oferta;
use App\Models\Resolucion;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.operacion')]
class Buscador extends Component
{
    use ManejaEstadoBloqueado;

    public string $codigoResolucion = '';
    public string $codigoOferta = '';

    public function mount(): void
    {
        if (Gate::denies('contratos-servicios.buscar')) {
            $this->bloquearAcceso(
                mensaje: 'No tienes permiso para buscar contratos de servicio.',
                ruta: route('operacion.home'),
            );
        }
    }

    /**
     * Solo valida y REDIRIGE — no guarda Resolucion/Oferta como estado
     * del componente. Al usar redirect()->route(), el navegador hace una
     * navegación completa hacia Create::class, que resuelve la Oferta
     * de cero vía route model binding. Esto evita por completo el
     * problema de "Snapshot missing" que tenías al mantener el modelo
     * como propiedad pública entre dos pasos del mismo componente.
     */
    public function buscar()
    {
        $this->validate([
            'codigoResolucion' => 'required|string',
            'codigoOferta' => 'required|string',
        ]);

        $resolucion = Resolucion::where('codigo', trim($this->codigoResolucion))->first();

        if (! $resolucion) {
            $this->addError('codigoResolucion', "No existe ninguna resolución con el código '{$this->codigoResolucion}'.");
            return;
        }

        if ($resolucion->auth_status !== Resolucion::ESTADO_APROBADA) {
            $this->addError('codigoResolucion', "La resolución está en estado '{$resolucion->estadoLabel()}', debe estar Aprobada.");
            return;
        }

        $oferta = Oferta::where('codigo', trim($this->codigoOferta))->first();

        if (! $oferta) {
            $this->addError('codigoOferta', "No existe ninguna oferta con el código '{$this->codigoOferta}'.");
            return;
        }

        if ($oferta->resolucion_id !== $resolucion->id) {
            $this->addError('codigoOferta', "La oferta '{$oferta->codigo}' no pertenece a la resolución '{$resolucion->codigo}'.");
            return;
        }

        if ($oferta->auth_status !== Oferta::ESTADO_APROBADA) {
            $this->addError('codigoOferta', "La oferta está en estado '{$oferta->estadoLabel()}', debe estar Aprobada.");
            return;
        }

        if (ContratoServicio::where('oferta_id', $oferta->id)->exists()) {
            $this->addError('codigoOferta', "Ya existe un contrato generado para la oferta '{$oferta->codigo}'.");
            return;
        }

        return redirect()->route('contratos-servicios.create', $oferta);
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.contratos-servicios.buscador');
    }
}
