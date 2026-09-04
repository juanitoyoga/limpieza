<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\Resolucion;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use App\Livewire\Concerns\ManejaEstadoBloqueado;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Verificar extends Component
{
    use ManejaEstadoBloqueado;

    public Resolucion $resolucion;
    public $observaciones;
    public $acepta_responsabilidad = false;

    // Conteos reales, expuestos para mostrarlos en el panel lateral
    public int $participantesCount = 0;
    public int $serviciosCount = 0;

    protected $rules = [
        'acepta_responsabilidad' => 'accepted',
        'observaciones'          => 'required|min:5|string',
    ];
    public function mount(Resolucion $resolucion)
    {

        $this->resolucion = $resolucion->load([
            'barrio',
            'serviceType.catalogoServicios',   // ⭐ RELACIÓN ANIDADA
            'participantes',
            'resolucionServicios.catalogoServicio',  // ⭐ RELACIÓN ANIDADA
            'verificador',
            'aprobador',
            'rechazador',
        ]);
        $detallesBase = [
            'Resolución'       => $resolucion->codigo,
            'Título'           => $resolucion->titulo,
            'Fecha de emisión' => $resolucion->fecha_resolucion?->format('d/m/Y') ?? '—',
            'Estado actual'    => $resolucion->estadoLabel(),
        ];


        $check = Gate::inspect('resoluciones.verificar', $resolucion);

        if (! $check->allowed()) {
            $this->bloquearAcceso(
                mensaje: $check->message() ?: 'No tienes permisos para verificar esta resolución.',
                ruta: route('resoluciones.lista'),
                detalles: $detallesBase,
                titulo: 'Sin permisos',
                nivel: 'warning'
            );
            return;
        }

        if ($resolucion->auth_status !== Resolucion::ESTADO_PENDIENTE) {
            $this->bloquearAcceso(
                mensaje: 'Esta resolución no está pendiente de verificación.',
                ruta: route('resoluciones.lista'),
                detalles: $detallesBase + ['Estado requerido' => Resolucion::ESTADO_PENDIENTE],
                titulo: 'Estado incorrecto',
                nivel: 'warning'
            );

            return;
        }

        $this->refreshConteos();
    }


    private function refreshConteos(): void
    {
        $this->participantesCount = $this->resolucion->participantes()->count();
        $this->serviciosCount = $this->resolucion->resolucionServicios()->count();
    }

    /**
     * Compara lo declarado en la resolución (numero_firmas / numero_servicios)
     * contra los registros realmente ingresados en participantes/servicios.
     * Ambos campos deben estar definidos y coincidir exactamente.
     */
    private function conteosValidos(): bool
    {
        if (is_null($this->resolucion->numero_firmas) || is_null($this->resolucion->numero_servicios)) {
            return false;
        }

        return $this->participantesCount === (int) $this->resolucion->numero_firmas
            && $this->serviciosCount === (int) $this->resolucion->numero_servicios;
    }

    public function save()
    {
        $this->validate();

        $this->refreshConteos();

        if (!$this->conteosValidos()) {
            $this->addError(
                'global',
                "No se puede verificar: se especificaron {$this->resolucion->numero_firmas} firma(s) y {$this->resolucion->numero_servicios} servicio(s), " .
                    "pero hay {$this->participantesCount} participante(s) y {$this->serviciosCount} servicio(s) registrados. " .
                    "Complete los registros faltantes antes de verificar."
            );
            return;
        }

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
        return $this->renderBloqueadoOr('livewire.operacion.resoluciones.verificar');
    }
}
