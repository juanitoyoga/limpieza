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
class Verificar extends Component
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

        $this->contrato = $contrato->load([
            'proveedor',
            'detalles.catalogoServicio',
            'formaPago.catalogoServicio',
        ]);


        $detallesBase = ['Contrato' => $contrato->codigo, 'Estado actual' => $contrato->estadoLabel()];

        $check = Gate::inspect('contratos-servicios.verificar', $contrato);
        if (! $check->allowed()) {
            $this->bloquearAcceso($check->message() ?: 'No tienes permisos para verificar este contrato.', route('contratos-servicios.lista'), $detallesBase, 'Sin permisos');
            return;
        }

        if ($contrato->auth_status !== ContratoServicio::ESTADO_PENDIENTE) {
            $this->bloquearAcceso('Este contrato no está pendiente de verificación.', route('contratos-servicios.lista'), $detallesBase + ['Estado requerido' => ContratoServicio::ESTADO_PENDIENTE], 'Estado incorrecto');
            return;
        }

        if ($contrato->detalles()->count() === 0) {
            $this->bloquearAcceso('Este contrato no tiene servicios registrados; no puede verificarse.', route('contratos-servicios.show', $contrato), $detallesBase, 'Sin servicios');
            return;
        }

        // Nuevo: el contrato debe tener al menos una línea de forma de pago
        if ($contrato->formaPago()->count() === 0) {
            $this->bloquearAcceso(
                'Este contrato no tiene forma de pago registrada; no puede verificarse.',
                route('contratos-servicios.show', $contrato),
                $detallesBase,
                'Sin forma de pago'
            );
            return;
        }

        // Nuevo: consistencia contra la oferta ganadora — número de servicios
        // y número de líneas de forma de pago deben coincidir exactamente.
        $mensajeInconsistencia = $this->validarConsistenciaConOferta($contrato);
        if ($mensajeInconsistencia) {
            $this->bloquearAcceso(
                $mensajeInconsistencia,
                route('contratos-servicios.show', $contrato),
                $detallesBase,
                'Inconsistencia con la oferta'
            );
            return;
        }
    }

    /**
     * Compara cantidad de servicios y de líneas de forma de pago del
     * contrato contra la oferta ganadora que le dio origen. Devuelve un
     * mensaje descriptivo si hay discrepancia, o null si todo coincide.
     *
     * No compara montos/valores línea por línea — solo cantidades — ya
     * que el objetivo es detectar líneas faltantes o de más (ej. una
     * edición manual posterior a la generación del contrato), no
     * pequeñas diferencias de redondeo.
     */
    private function validarConsistenciaConOferta(ContratoServicio $contrato): ?string
    {
        $oferta = $contrato->oferta;

        if (! $oferta) {
            return 'El contrato no tiene una oferta de origen asociada; no se puede verificar la consistencia.';
        }

        $serviciosContrato = $contrato->detalles()->count();
        $serviciosOferta   = $oferta->ofertaServicios()->count();

        if ($serviciosContrato !== $serviciosOferta) {
            return "El número de servicios del contrato ({$serviciosContrato}) no coincide con "
                . "el de la oferta ganadora ({$serviciosOferta}). Revise el detalle antes de continuar.";
        }

        $formaPagoContrato = $contrato->formaPago()->count();
        $formaPagoOferta   = $oferta->formaPago()->count();

        if ($formaPagoContrato !== $formaPagoOferta) {
            return "El número de líneas de forma de pago del contrato ({$formaPagoContrato}) no coincide con "
                . "el de la oferta ganadora ({$formaPagoOferta}). Revise el plan de pago antes de continuar.";
        }

        return null;
    }

    public function save()
    {
        Gate::authorize('contratos-servicios.verificar', $this->contrato);

        if ($this->contrato->auth_status !== ContratoServicio::ESTADO_PENDIENTE) {
            $this->addError('global', 'Este contrato ya no está pendiente.');
            return;
        }

        // Revalidar consistencia server-side justo antes de guardar, por si
        // algo cambió entre el mount() y el submit (ej. otro usuario editó
        // los servicios o la forma de pago mientras esta pantalla seguía abierta).
        $mensajeInconsistencia = $this->validarConsistenciaConOferta($this->contrato);
        if ($mensajeInconsistencia) {
            $this->addError('global', $mensajeInconsistencia);
            return;
        }

        $this->validate();

        $user = Auth::user();

        try {
            $evento = DB::transaction(function () use ($user) {
                $this->contrato->update([
                    'verificado_por'     => $user->id,
                    'fecha_verificacion' => now(),
                    'observaciones'      => trim(($this->contrato->observaciones ?? '') . ' | VERIF: ' . $this->observaciones),
                    'auth_status'        => ContratoServicio::ESTADO_VERIFICADA,
                ]);

                return AuditEvent::logEvent($this->contrato, $user->id, AuditEvent::EVENT_CONTRATO_SERVICIO_VERIFICADO, ['message' => $this->observaciones]);
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'contrato_servicio_verificacion', $e);
            $this->addError('global', 'Error al verificar el contrato.');
            return;
        }

        session()->flash('success', 'Contrato verificado correctamente.');
        return redirect()->route('contratos-servicios.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.contratos-servicios.verificar');
    }
}
