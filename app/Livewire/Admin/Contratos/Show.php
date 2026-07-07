<?php

namespace App\Livewire\Admin\Contratos;

use App\Models\Barrio;
use App\Models\Contrato;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]


class Show extends Component
{
    public Contrato $contrato;

    public bool $mostrarModalVerificar = false;
    public bool $mostrarModalAprobar = false;
    public bool $mostrarModalRechazar = false;

    public bool   $modalRechazo  = false;
    public string $motivo_rechazo = '';

    public function mount(Contrato $contrato): void
    {
        $this->contrato = $contrato;
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────


    public function abrirModalVerificar()
    {
        $this->mostrarModalVerificar = true;
    }

    public function cerrarModalVerificar()
    {
        $this->mostrarModalVerificar = false;
    }
    public function abrirModalRechazar()
    {
        $this->mostrarModalRechazar = true;
    }

    public function cerrarModalRechazar()
    {
        $this->mostrarModalRechazar = false;
    }

    public function abrirModalAprobar()
    {
        $this->mostrarModalAprobar = true;
    }

    public function cerrarModalAprobar()
    {
        $this->mostrarModalAprobar = false;
    }

    private function funcionarioActual(): int
    {
        return auth()->id();
    }

    private function rolActual(): string
    {
        return auth()->user()->role_name ?? 'Sin rol';
    }

    // ─── Acciones ───────────────────────────────────────────────────────────────

    public function verificar(): void
    {
        $this->authorize('verificar', $this->contrato);

        try {
            $this->contrato->registrarVerificacion(
                $this->funcionarioActual(),
                $this->rolActual()
            );

            $this->contrato->refresh();
            session()->flash('success', 'Contrato verificado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function aprobar(): void
    {
        $this->authorize('aprobar', $this->contrato);

        try {
            $this->contrato->registrarAprobacion(
                $this->funcionarioActual(),
                $this->rolActual()
            );

            $this->contrato->refresh();
            session()->flash('success', 'Contrato aprobado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function rechazar(): void
    {
        $this->validate([
            'motivo_rechazo' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'motivo_rechazo.required' => 'El motivo del rechazo es obligatorio.',
            'motivo_rechazo.min'      => 'El motivo debe tener al menos 10 caracteres.',
            'motivo_rechazo.max'      => 'El motivo no puede superar los 500 caracteres.',
        ]);

        $this->contrato->update([
            'estado'          => Contrato::ESTADO_RECHAZADO,
            'id_rechazo'      => $this->funcionarioActual(),
            'rol_rechazo'     => $this->rolActual(),
            'motivo_rechazo'  => $this->motivo_rechazo,
            'fecha_rechazo'   => now(),
        ]);

        $this->contrato->refresh();
        $this->modalRechazo  = false;
        $this->motivo_rechazo = '';
        session()->flash('success', 'Contrato rechazado correctamente.');
    }

    // ─── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.contratos.show', [
            'contrato' => $this->contrato->load('barrio'),
        ]);
    }
}
