<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\ContratoServicio;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use App\Livewire\Concerns\ManejaEstadoBloqueado;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Rechazar extends Component
{
    use ManejaEstadoBloqueado;

    public ContratoServicio $contrato;
    public $observaciones;

    protected $rules = [
        'observaciones' => 'required|min:5|string',
    ];

    public function mount(ContratoServicio $contrato)
    {
        $this->contrato = $contrato;
        $detallesBase = ['Contrato' => $contrato->codigo, 'Estado actual' => $contrato->estadoLabel()];

        $check = Gate::inspect('contratos-servicios.rechazar', $contrato);
        if (! $check->allowed()) {
            $this->bloquearAcceso($check->message() ?: 'No tienes permisos para rechazar este contrato.', route('contratos-servicios.lista'), $detallesBase, 'Sin permisos');
            return;
        }

        // Rechazar solo tiene sentido antes de llegar a Aprobada — igual
        // que en Oferta/Resolucion.
        if (!in_array($contrato->auth_status, [ContratoServicio::ESTADO_PENDIENTE, ContratoServicio::ESTADO_VERIFICADA])) {
            $this->bloquearAcceso('Solo se puede rechazar un contrato Pendiente o Verificado.', route('contratos-servicios.lista'), $detallesBase, 'Estado incorrecto');
            return;
        }
    }

    public function save()
    {
        Gate::authorize('contratos-servicios.rechazar', $this->contrato);

        if (!in_array($this->contrato->auth_status, [ContratoServicio::ESTADO_PENDIENTE, ContratoServicio::ESTADO_VERIFICADA])) {
            $this->addError('global', 'Este contrato ya no puede rechazarse en su estado actual.');
            return;
        }

        $this->validate();
        $user = Auth::user();

        try {
            $evento = DB::transaction(function () use ($user) {
                $this->contrato->update([
                    'rechazado_por' => $user->id,
                    'fecha_rechazo' => now(),
                    'observaciones' => trim(($this->contrato->observaciones ?? '') . ' | RECHAZO: ' . $this->observaciones),
                    'auth_status'   => ContratoServicio::ESTADO_RECHAZADA,
                ]);

                return AuditEvent::logEvent($this->contrato, $user->id, AuditEvent::EVENT_CONTRATO_SERVICIO_RECHAZADO, ['message' => $this->observaciones]);
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'contrato_servicio_rechazo', $e);
            $this->addError('global', 'Error al rechazar el contrato.');
            return;
        }

        session()->flash('success', 'Contrato rechazado.');
        return redirect()->route('contratos-servicios.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.contratos-servicios.rechazar');
    }
}
