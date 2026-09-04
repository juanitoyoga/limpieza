<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\Resolucion;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Concerns\ManejaEstadoBloqueado;

#[Layout('layouts.operacion')]
class Rechazar extends Component
{
    use ManejaEstadoBloqueado;
    public Resolucion $resolucion;
    public $observaciones;
    public $acepta_responsabilidad = false;
    public int $participantesCount = 0;
    public int $serviciosCount = 0;

    protected $rules = [
        'acepta_responsabilidad' => 'accepted',
        'observaciones'          => 'required|min:5|string',
    ];

    public function mount(Resolucion $resolucion)
    {
        $this->resolucion = $resolucion->load([
            'barrio',
            'serviceType.catalogoServicios',   // ⭐ RELACIÓN ANIDADA
            'participantes',
            'resolucionServicios.catalogoServicio',  // ⭐ RELACIÓN ANIDADA
            'verificador',
            'aprobador',
            'rechazador',
        ]);

        $this->refreshConteos();

        Gate::authorize('resoluciones.rechazar', $resolucion);

        if (!in_array($resolucion->auth_status, [Resolucion::ESTADO_PENDIENTE, Resolucion::ESTADO_VERIFICADA])) {
            abort(403, 'Esta resolución no puede ser rechazada en su estado actual.');
        }
    }

    private function refreshConteos(): void
    {
        $this->participantesCount = $this->resolucion->participantes()->count();
        $this->serviciosCount = $this->resolucion->resolucionServicios()->count();
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        try {
            $auditEvent = DB::transaction(function () use ($user) {
                $this->resolucion->update([
                    'rechazado_por' => $user->id,
                    'fecha_rechazo' => now(),
                    'observaciones' => trim(($this->resolucion->observaciones ?? '') . ' | RECHAZO: ' . $this->observaciones),
                    'auth_status'   => Resolucion::ESTADO_RECHAZADA,
                ]);

                return AuditEvent::logEvent(
                    $this->resolucion,
                    $user->id,
                    'resolucion_rechazada',
                    ['message' => $this->observaciones]
                );
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($auditEvent->id));
        } catch (\Throwable $e) {
            $this->addError('global', 'Error al rechazar la resolución: ' . $e->getMessage());
            return;
        }

        session()->flash('success', 'Resolución rechazada correctamente.');
        return redirect()->route('resoluciones.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.resoluciones.rechazar');
    }
}
