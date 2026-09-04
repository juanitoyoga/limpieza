<?php
// app/Livewire/Operacion/Ofertas/Rechazar.php

namespace App\Livewire\Operacion\Ofertas;

use App\Models\Oferta;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Rechazar extends Component
{
    public Oferta $oferta;
    public string $observaciones = '';

    protected $rules = ['observaciones' => 'required|string|min:10'];

    public function mount(Oferta $oferta)
    {
        Gate::authorize('ofertas.rechazar', $oferta);

        abort_unless(
            in_array($oferta->auth_status, [Oferta::ESTADO_PENDIENTE, Oferta::ESTADO_VERIFICADA], true),
            403,
            'Esta oferta ya fue aprobada o rechazada.'
        );

        $this->oferta = $oferta;
    }

    public function confirmar()
    {
        $this->validate();
        $userId = Auth::id();

        DB::transaction(function () use ($userId) {
            $this->oferta->update([
                'auth_status'   => Oferta::ESTADO_RECHAZADA,
                'rechazado_por' => $userId,
                'fecha_rechazo' => now(),
                'observaciones' => trim(($this->oferta->observaciones ?? '') . "\nRechazo: {$this->observaciones}"),
            ]);

            $evento = AuditEvent::logEvent($this->oferta, $userId, 'oferta_rechazada', [
                'codigo' => $this->oferta->codigo,
                'observaciones' => $this->observaciones,
            ]);

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        });

        session()->flash('message', 'Oferta rechazada.');

        return redirect()->route('ofertas.lista');
    }

    public function render()
    {
        return view('livewire.operacion.ofertas.rechazar');
    }
}
