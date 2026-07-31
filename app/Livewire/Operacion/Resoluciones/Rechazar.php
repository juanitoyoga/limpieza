<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\Resolucion;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Rechazar extends Component
{
    public Resolucion $resolucion;
    public $observaciones;
    public $acepta_responsabilidad = false;

    protected $rules = [
        'acepta_responsabilidad' => 'accepted',
        'observaciones'          => 'required|min:5|string',
    ];

    public function mount(Resolucion $resolucion)
    {
        Gate::authorize('resoluciones.rechazar', $resolucion);

        if (!in_array($resolucion->auth_status, [Resolucion::ESTADO_PENDIENTE, Resolucion::ESTADO_VERIFICADA])) {
            abort(403, 'Esta resolución no puede ser rechazada en su estado actual.');
        }

        $this->resolucion = $resolucion;
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
        return view('livewire.operacion.resoluciones.rechazar');
    }
}
