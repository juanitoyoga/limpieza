<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\AsignacionContratoServicio;
use App\Models\Contacto;
use App\Models\ContratoServicio;
use App\Services\ContratistaAsignacionService;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
/**
 * Lista los contactos activos del proveedor de un ContratoServicio Aprobada
 * y permite asignarlos/revocarlos como Contratista de ESE contrato.
 *
 * Regla de Livewire del proyecto: nunca se guarda el modelo Eloquent en una
 * propiedad pública — solo el id, resuelto vía #[Computed].
 */
class AsignarPersonal extends Component
{
    public int $contratoServicioId;

    public ContratoServicio $contrato;

    public function mount(ContratoServicio $contrato)
    {
        $this->contrato = $contrato;
        $this->contratoServicioId = $contrato->id;

        \Illuminate\Support\Facades\Gate::authorize('asignar-contratistas', $this->contrato);
    }

    #[Computed]
    public function contrato(): ContratoServicio
    {
        return ContratoServicio::with('resolucion')->findOrFail($this->contratoServicioId);
    }

    /**
     * Contactos activos del proveedor, con su Contratista (si existe) y la
     * asignación a ESTE contrato específico (si existe), para poder pintar
     * el estado correcto de cada fila sin N+1.
     */
    #[Computed]
    public function contactos()
    {
        return Contacto::query()
            ->where('proveedor_id', $this->contrato->proveedor_id)
            ->where('is_active', true)
            ->with([
                'contratista.user',
                'contratista.asignaciones' => fn($q) => $q->where('contrato_servicio_id', $this->contratoServicioId),
            ])
            ->orderBy('first_name')
            ->get();
    }

    public function asignar(int $contactoId, ContratistaAsignacionService $service): void
    {
        $contacto = Contacto::findOrFail($contactoId);

        try {
            $service->asignar($contacto, $this->contrato, auth()->user());
            session()->flash('success', "{$contacto->nombre_completo} fue asignado a este contrato.");
        } catch (\DomainException $e) {
            $this->addError('asignacion', $e->getMessage());
        }

        unset($this->contactos); // limpia el memo del computed para reflejar el cambio
    }

    public function revocar(int $asignacionId, ContratistaAsignacionService $service): void
    {
        $asignacion = AsignacionContratoServicio::findOrFail($asignacionId);

        if ($asignacion->contrato_servicio_id !== $this->contratoServicioId) {
            abort(403);
        }

        $service->revocarAsignacion($asignacion);

        session()->flash('success', 'Se revocó el acceso de ese contratista a este contrato.');

        unset($this->contactos);
    }

    public function render()
    {
        return view('livewire.operacion.contratos-servicios.asignarpersonal');
    }
}
