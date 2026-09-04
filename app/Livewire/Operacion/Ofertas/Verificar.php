<?php
// app/Livewire/Operacion/Ofertas/Verificar.php

namespace App\Livewire\Operacion\Ofertas;

use App\Models\Oferta;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Concerns\ManejaEstadoBloqueado;

#[Layout('layouts.operacion')]
class Verificar extends Component
{

    use ManejaEstadoBloqueado;

    public Oferta $oferta;
    public bool $documentoRevisado = false;
    public string $observaciones = '';

    protected $rules = [
        'documentoRevisado' => 'accepted',
        'observaciones' => 'nullable|string',
    ];

    protected $messages = [
        'documentoRevisado.accepted' => 'Debes confirmar que revisaste el documento físico contra el hash registrado.',
    ];

    public function mount(Oferta $oferta)
    {

        $this->oferta = $oferta;


        if ($oferta->auth_status !== Oferta::ESTADO_PENDIENTE) {
            $this->bloquearAcceso(
                mensaje: 'Esta oferta ya no está en estado Pendiente.',
                ruta: route('ofertas.show', $oferta),
                detalles: ['Estado actual' => $oferta->estadoLabel()],
            );
            return;
        }
    }

    public function confirmar()
    {
        $this->validate();

        $userId = Auth::id();

        DB::transaction(function () use ($userId) {
            $this->oferta->update([
                'auth_status'        => Oferta::ESTADO_VERIFICADA,
                'verificado_por'     => $userId,
                'fecha_verificacion' => now(),
                'observaciones'      => trim(($this->oferta->observaciones ?? '') . "\n{$this->observaciones}"),
            ]);

            $evento = AuditEvent::logEvent($this->oferta, $userId, 'oferta_verificada', [
                'codigo' => $this->oferta->codigo,
                'documento_hash' => $this->oferta->documento_original_hash,
            ]);

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        });

        session()->flash('message', 'Oferta verificada correctamente.');

        return redirect()->route('ofertas.lista');
    }

    public function render()
    {

        return $this->renderBloqueadoOr('livewire.operacion.ofertas.verificar');
    }
}
