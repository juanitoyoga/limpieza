<?php

namespace App\Livewire\Operacion\Ofertas;

use Livewire\Component;
use App\Models\Oferta;
use App\Models\AuditEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Jobs\RegistrarEventoBlockchain;

class Rechazar extends Component
{
    public Oferta $oferta;
    public $observaciones = '';

    public function mount(Oferta $oferta)
    {
        Gate::authorize('ofertas.reject');

        $this->oferta = $oferta;

        if ($oferta->auth_status !== Oferta::ESTADO_VERIFICADA) {
            abort(403, 'Solo las ofertas verificadas pueden ser rechazadas.');
        }
    }

    public function rechazar()
    {
        DB::transaction(function () {
            $userId = Auth::id();

            $this->oferta->update([
                'auth_status'       => Oferta::ESTADO_RECHAZADA,
                'rechazado_por'     => $userId,
                'fecha_rechazo'     => now(),
                'observaciones'     => $this->observaciones,
            ]);

            $audit = AuditEvent::logEvent(
                $this->oferta,
                $userId,
                'oferta_rechazada',
                [
                    'codigo' => $this->oferta->codigo,
                    'proveedor' => $this->oferta->proveedor_id,
                    'resolucion' => $this->oferta->resolucion_id,
                    'observaciones' => $this->observaciones,
                ]
            );

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($audit->id));
        });

        session()->flash('message', 'Oferta rechazada correctamente.');
        return redirect()->route('ofertas.lista');
    }

    public function render()
    {
        return view('livewire.operacion.ofertas.rechazar');
    }
}
