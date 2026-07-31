<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\Resolucion;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Verificar extends Component
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
        Gate::authorize('resoluciones.verificar', $resolucion);

        if ($resolucion->auth_status !== Resolucion::ESTADO_PENDIENTE) {
            abort(403, 'Esta resolución no está pendiente de verificación.');
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
                    'verificado_por'      => $user->id,
                    'fecha_verificacion'  => now(),
                    'observaciones'       => trim(($this->resolucion->observaciones ?? '') . ' | VERIF: ' . $this->observaciones),
                    'auth_status'         => Resolucion::ESTADO_VERIFICADA,
                ]);

                return AuditEvent::logEvent(
                    $this->resolucion,
                    $user->id,
                    'resolucion_verificada',
                    ['message' => $this->observaciones]
                );
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($auditEvent->id));
        } catch (\Throwable $e) {
            $this->addError('global', 'Error al verificar la resolución: ' . $e->getMessage());
            return;
        }

        session()->flash('success', 'Resolución verificada correctamente.');
        return redirect()->route('resoluciones.lista');
    }

    public function render()
    {
        return view('livewire.operacion.resoluciones.verificar');
    }
}
