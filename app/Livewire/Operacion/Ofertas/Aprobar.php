<?php
// app/Livewire/Operacion/Ofertas/Aprobar.php

namespace App\Livewire\Operacion\Ofertas;

use App\Models\{Oferta, ResolucionServicio, AuditEvent};

use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate};
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Concerns\ManejaEstadoBloqueado;

#[Layout('layouts.operacion')]
class Aprobar extends Component
{

    use ManejaEstadoBloqueado;


    public Oferta $oferta;
    public bool $aceptaClausula = false;
    public string $observaciones = '';

    protected $rules = [
        'aceptaClausula' => 'accepted',
        'observaciones'  => 'nullable|string',
    ];

    protected $messages = [
        'aceptaClausula.accepted' => 'Debe aceptar la cláusula legal para aprobar la oferta.',
    ];

    public function mount(Oferta $oferta)
    {

        $this->oferta = $oferta->load([
            'proveedor',
            'resolucion',
            'verificador',
            'ofertaServicios.catalogoServicio',
            'formaPago.catalogoServicio',
        ]);

        if (Gate::denies('ofertas.aprobar', $oferta)) {
            $this->bloquearAcceso(
                mensaje: 'No tienes permiso para aprobar esta oferta.',
                ruta: route('ofertas.show', $oferta),
            );
            return;
        }

        if ($oferta->auth_status !== Oferta::ESTADO_VERIFICADA) {
            $this->bloquearAcceso(
                mensaje: 'Solo se pueden aprobar ofertas ya Verificadas.',
                ruta: route('ofertas.show', $oferta),
                detalles: ['Estado actual' => $oferta->estadoLabel()],
            );
            return;
        }

        if ($this->oferta->ofertaServicios->isEmpty()) {
            $this->bloquearAcceso(
                mensaje: 'Esta oferta no tiene servicios registrados; no puede aprobarse.',
                ruta: route('ofertas.show', $oferta),
            );
            return;
        }

        if ($this->oferta->formaPago->isEmpty()) {
            $this->bloquearAcceso(
                mensaje: 'Esta oferta no tiene forma de pago registrada; no puede aprobarse.',
                ruta: route('ofertas.show', $oferta),
            );
            return;
        }

        $yaTieneGanadora = Oferta::where('resolucion_id', $oferta->resolucion_id)
            ->where('id', '!=', $oferta->id)
            ->where('auth_status', Oferta::ESTADO_APROBADA)
            ->first();

        if ($yaTieneGanadora) {
            $this->bloquearAcceso(
                mensaje: 'Esta resolución ya tiene una oferta aprobada. No puede existir más de una oferta ganadora por resolución.',
                ruta: route('ofertas.show', $oferta),
                detalles: ['Oferta ya aprobada' => $yaTieneGanadora->codigo],
                nivel: 'error',
            );
            return;
        }
    }


    public function aprobar()
    {
        return $this->confirmar();
    }


    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.ofertas.aprobar', [
            'oferta' => $this->oferta,
        ]);
    }

    public function confirmar()
    {
        $userId = Auth::id();

        try {
            DB::transaction(function () use ($userId) {
                // Re-chequeo DENTRO de la transacción con lock: si dos
                // administradores aprueban dos ofertas distintas de la
                // misma resolución casi al mismo tiempo, el segundo en
                // llegar aquí debe fallar — lockForUpdate() serializa
                // esto contra cualquier otra transacción concurrente
                // que intente lo mismo.
                $yaTieneGanadora = Oferta::where('resolucion_id', $this->oferta->resolucion_id)
                    ->where('id', '!=', $this->oferta->id)
                    ->where('auth_status', Oferta::ESTADO_APROBADA)
                    ->lockForUpdate()
                    ->exists();

                if ($yaTieneGanadora) {
                    throw new \RuntimeException('resolucion_ya_tiene_ganadora');
                }

                $this->oferta->update([
                    'auth_status'      => Oferta::ESTADO_APROBADA,
                    'aprobado_por'     => $userId,
                    'fecha_aprobacion' => now(),
                    'observaciones'    => trim(($this->oferta->observaciones ?? '') . "\n{$this->observaciones}"),
                ]);

                $rechazadas = $this->oferta->rechazarCompetidoras($userId);

                // Las líneas de la oferta ganadora quedan marcadas como Aprobada
                // en la resolución — es la única sincronización de estado que
                // se hace a nivel de línea; las competidoras solo se marcan
                // como Rechazada a nivel de oferta completa (rechazarCompetidoras).
                foreach ($this->oferta->ofertaServicios as $item) {
                    if ($item->resolucion_servicio_id) {
                        ResolucionServicio::where('id', $item->resolucion_servicio_id)
                            ->update(['estado' => 'Aprobada']);
                    }
                }

                $evento = AuditEvent::logEvent($this->oferta, $userId, 'oferta_aprobada', [
                    'codigo'        => $this->oferta->codigo,
                    'monto_total'   => $this->oferta->monto_total,
                    'competidoras_rechazadas' => $rechazadas->pluck('codigo'),
                ]);

                DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'resolucion_ya_tiene_ganadora') {
                $this->bloquearAcceso(
                    mensaje: 'Otra oferta de esta misma resolución fue aprobada justo ahora por otro usuario. Esta operación se canceló para evitar dos ofertas ganadoras.',
                    ruta: route('ofertas.show', $this->oferta),
                    nivel: 'error',
                );
                return;
            }
            throw $e;
        }

        session()->flash('message', 'Oferta aprobada. Las demás ofertas de esta resolución fueron rechazadas automáticamente.');

        return redirect()->route('ofertas.lista');
    }
}
