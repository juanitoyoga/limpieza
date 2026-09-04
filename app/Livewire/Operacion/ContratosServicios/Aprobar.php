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
class Aprobar extends Component
{
    use ManejaEstadoBloqueado;

    public ContratoServicio $contrato;
    public $observaciones;
    public $acepta_responsabilidad = false;

    protected $rules = [
        'acepta_responsabilidad' => 'accepted',
        'observaciones'          => 'required|min:5|string',
    ];


    public function mount(ContratoServicio $contrato)
    {
        $this->contrato = $contrato;
        $detallesBase = ['Contrato' => $contrato->codigo, 'Estado actual' => $contrato->estadoLabel()];

        $check = Gate::inspect('contratos-servicios.aprobar', $contrato);
        if (! $check->allowed()) {
            $this->bloquearAcceso($check->message() ?: 'No tienes permisos para aprobar este contrato.', route('contratos-servicios.lista'), $detallesBase, 'Sin permisos');
            return;
        }

        if ($contrato->auth_status !== ContratoServicio::ESTADO_VERIFICADA) {
            $this->bloquearAcceso('Este contrato no está verificado, no puede aprobarse todavía.', route('contratos-servicios.lista'), $detallesBase + ['Estado requerido' => ContratoServicio::ESTADO_VERIFICADA], 'Estado incorrecto');
            return;
        }
    }

    public function save()
    {
        Gate::authorize('contratos-servicios.aprobar', $this->contrato);

        if ($this->contrato->auth_status !== ContratoServicio::ESTADO_VERIFICADA) {
            $this->addError('global', 'Este contrato ya no está verificado.');
            return;
        }

        $this->validate();

        $user = Auth::user();

        // Segregación de funciones, igual que en Resolucion/Oferta.
        if ($user->id === $this->contrato->verificado_por) {
            $this->addError('global', 'El aprobador no puede ser el mismo que verificó el contrato.');
            return;
        }

        try {
            $evento = DB::transaction(function () use ($user) {
                $this->contrato->update([
                    'aprobado_por'     => $user->id,
                    'fecha_aprobacion' => now(),
                    'observaciones'    => trim(($this->contrato->observaciones ?? '') . ' | APROBACIÓN: ' . $this->observaciones),
                    'auth_status'      => ContratoServicio::ESTADO_APROBADA,
                ]);

                return AuditEvent::logEvent($this->contrato, $user->id, AuditEvent::EVENT_CONTRATO_SERVICIO_APROBADO, ['message' => $this->observaciones]);
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'contrato_servicio_aprobacion', $e);
            $this->addError('global', 'Error al aprobar el contrato.');
            return;
        }

        session()->flash('success', 'Contrato aprobado y vigente.');
        return redirect()->route('contratos-servicios.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.contratos-servicios.aprobar');
    }
}
