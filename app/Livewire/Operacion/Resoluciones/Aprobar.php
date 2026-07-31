<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\Resolucion;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Aprobar extends Component
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
        Gate::authorize('resoluciones.aprobar', $resolucion);

        if ($resolucion->auth_status !== Resolucion::ESTADO_VERIFICADA) {
            abort(403, 'Esta resolución no está verificada, no puede aprobarse todavía.');
        }

        $this->resolucion = $resolucion;
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        if ($user->id === $this->resolucion->verificado_por) {
            $this->addError('global', 'El aprobador no puede ser el mismo que verificó la resolución.');
            return;
        }

        try {
            $auditEvent = DB::transaction(function () use ($user) {
                $this->resolucion->update([
                    'aprobado_por'     => $user->id,
                    'fecha_aprobacion' => now(),
                    'observaciones'    => trim(($this->resolucion->observaciones ?? '') . ' | APROBACIÓN: ' . $this->observaciones),
                    'auth_status'      => Resolucion::ESTADO_APROBADA,
                ]);

                return AuditEvent::logEvent(
                    $this->resolucion,
                    $user->id,
                    'resolucion_aprobada',
                    ['message' => $this->observaciones]
                );
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($auditEvent->id));
        } catch (\Throwable $e) {
            $this->addError('global', 'Error al aprobar la resolución: ' . $e->getMessage());
            return;
        }

        session()->flash('success', 'Resolución aprobada correctamente.');
        return redirect()->route('resoluciones.lista');
    }

    public function render()
    {
        return view('livewire.operacion.resoluciones.aprobar');
    }
}
