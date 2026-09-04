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
class Aprobar extends Component
{
    use ManejaEstadoBloqueado;

    public Resolucion $resolucion;
    public $observaciones;
    public $acepta_responsabilidad = false;
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

        $this->refreshConteos();
        // Detalles base, presentes en cualquier tipo de bloqueo relacionado
        // con esta resolución.
        $detallesBase = [
            'Resolución'       => $resolucion->codigo,
            'Título'           => $resolucion->titulo,
            'Fecha de emisión' => $resolucion->fecha_resolucion?->format('d/m/Y') ?? '—',
            'Estado actual'    => $resolucion->estadoLabel(),
        ];

        $check = Gate::inspect('resoluciones.aprobar', $resolucion);
        if (! $check->allowed()) {
            $this->bloquearAcceso(
                mensaje: $check->message() ?: 'No tienes permisos para aprobar esta resolución.',
                ruta: route('resoluciones.lista'),
                detalles: $detallesBase,
                titulo: 'Sin permisos',
                nivel: 'warning'
            );
            return;
        }

        if ($resolucion->auth_status !== Resolucion::ESTADO_VERIFICADA) {
            $this->bloquearAcceso(
                mensaje: 'Esta resolución no está verificada, no puede aprobarse todavía.',
                ruta: route('resoluciones.lista'),
                detalles: $detallesBase + ['Estado requerido' => Resolucion::ESTADO_VERIFICADA],
                titulo: 'Estado incorrecto',
                nivel: 'warning'
            );
            return;
        }

        $participantesCount = $resolucion->participantes()->count();
        $serviciosCount = $resolucion->resolucionServicios()->count();

        if (
            is_null($resolucion->numero_firmas)
            || is_null($resolucion->numero_servicios)
            || $participantesCount !== (int) $resolucion->numero_firmas
            || $serviciosCount !== (int) $resolucion->numero_servicios
        ) {
            $this->bloquearAcceso(
                mensaje: 'Los registros de participantes o servicios no coinciden con lo declarado en la resolución.',
                ruta: route('resoluciones.lista'),
                detalles: $detallesBase + [
                    'Firmas declaradas'        => $resolucion->numero_firmas ?? '—',
                    'Participantes ingresados' => $participantesCount,
                    'Servicios declarados'     => $resolucion->numero_servicios ?? '—',
                    'Servicios ingresados'     => $serviciosCount,
                ],
                titulo: 'Datos inconsistentes',
                nivel: 'error'
            );
            return;
        }
    }
    private function refreshConteos(): void
    {
        $this->participantesCount = $this->resolucion->participantes()->count();
        $this->serviciosCount = $this->resolucion->resolucionServicios()->count();
    }

    public function save()
    {
        // Re-validación OBLIGATORIA: mount() solo corrió una vez al cargar
        // la página, así que estas comprobaciones son la barrera real de
        // seguridad — no basta con haber marcado $bloqueado en mount().
        Gate::authorize('resoluciones.aprobar', $this->resolucion);

        if ($this->resolucion->auth_status !== Resolucion::ESTADO_VERIFICADA) {
            $this->addError('global', 'Esta resolución ya no está en estado verificado.');
            return;
        }

        $this->refreshConteos();

        if (
            $this->participantesCount !== (int) $this->resolucion->numero_firmas
            || $this->serviciosCount !== (int) $this->resolucion->numero_servicios
        ) {
            $this->addError('global', 'Los registros de esta resolución cambiaron y ya no son consistentes.');
            return;
        }

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
        return $this->renderBloqueadoOr('livewire.operacion.resoluciones.aprobar');
    }
}
