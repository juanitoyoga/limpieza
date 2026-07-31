<?php

namespace App\Livewire\Operacion\Ofertas;

use Livewire\Component;
use App\Models\Oferta;
use App\Models\AuditEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Jobs\RegistrarEventoBlockchain;

class Aprobar extends Component
{
    public Oferta $oferta;
    public $observaciones = '';

    public function mount(Oferta $oferta)
    {
        Gate::authorize('ofertas.approve');

        $this->oferta = $oferta;

        if ($oferta->auth_status !== Oferta::ESTADO_VERIFICADA) {
            abort(403, 'Solo las ofertas verificadas pueden ser aprobadas.');
        }
    }

    public function aprobar()
    {
        DB::transaction(function () {
            $userId = Auth::id();

            $this->oferta->update([
                'auth_status'       => Oferta::ESTADO_APROBADA,
                'aprobado_por'      => $userId,
                'fecha_aprobacion'  => now(),
                'observaciones'     => $this->observaciones,
            ]);

            $audit = AuditEvent::logEvent(
                $this->oferta,
                $userId,
                'oferta_aprobada',
                [
                    'codigo' => $this->oferta->codigo,
                    'proveedor' => $this->oferta->proveedor_id,
                    'resolucion' => $this->oferta->resolucion_id,
                    'observaciones' => $this->observaciones,
                ]
            );

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($audit->id));
        });

        session()->flash('message', 'Oferta aprobada correctamente.');
        return redirect()->route('ofertas.lista');
    }

    public function render()
    {
        return view('livewire.operacion.ofertas.aprobar');
    }
}
