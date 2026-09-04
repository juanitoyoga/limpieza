<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\Resolucion;
use App\Models\ResolucionParticipante;
use App\Models\ResolucionServicio;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Show extends Component
{
    public Resolucion $resolucion;

    public bool $showParticipanteModal = false;
    public bool $showServicioModal = false;

    public ?int $participanteId = null;
    public ?int $servicioId = null;

    // ResolucionParticipante
    public $participante_user_id = null;
    public $participante_nombre_firmante = null;
    public $participante_documento_identidad = null;
    public $participante_cargo = null;
    public $participante_orden_firma = 1;

    // ResolucionServicio
    public $servicio_catalogo_servicio_id = null;
    public $servicio_cantidad = null;
    public $servicio_prioridad = null;
    public $servicio_observaciones = null;
    public $servicio_estado = null;
    public $servicio_costo_unitario = null;

    public bool $confirmingDelete = false;
    public ?int $deleteId = null;

    // 'participante' | 'servicio' — indica qué tipo de registro se va a borrar.
    // Antes, tanto el botón de participante como el de servicio llamaban al mismo
    // confirmDelete($id) sin distinguir el tipo, y solo existía deleteParticipante(),
    // así que borrar un servicio intentaba buscar ese ID dentro de participantes().
    public ?string $deleteType = null;

    public function mount(Resolucion $resolucion)
    {
        $this->resolucion = $resolucion->load([
            'barrio',
            'serviceType',
            'participantes',
            'resolucionServicios',
            'verificador',
            'aprobador',
            'rechazador',
        ]);
    }


    protected function rulesParticipantes(): array
    {
        return [
            'participante_user_id' => ['nullable', 'exists:users,id'],
            'participante_nombre_firmante' => ['required', 'string', 'max:255'],
            'participante_documento_identidad' => ['required', 'string', 'max:255'],
            'participante_cargo' => ['nullable', 'string', 'max:255'],
            'participante_orden_firma' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function messagesParticipantes(): array
    {
        return [
            'participante_nombre_firmante.required' => 'El nombre del firmante es obligatorio.',
            'participante_documento_identidad.required' => 'El documento de identidad es obligatorio.',
            'participante_orden_firma.required' => 'El orden de firma es obligatorio.',
            'participante_orden_firma.integer' => 'El orden de firma debe ser un número entero.',
            'participante_orden_firma.min' => 'El orden de firma debe ser al menos 1.',
        ];
    }

    protected function rulesServicios(): array
    {
        return [
            'servicio_catalogo_servicio_id' => ['required', 'exists:catalogo_servicios,id'],
            'servicio_cantidad' => ['required', 'integer', 'min:1'],
            'servicio_prioridad' => ['nullable', 'in:baja,media,alta,urgente'],
            'servicio_observaciones' => ['nullable', 'string'],
            'servicio_estado' => ['nullable', 'in:Pendiente,Verificada,Aprobada,Rechazada'],
            'servicio_costo_unitario' => ['nullable', 'numeric', 'min:0'],
        ];
    }
    /**
     * Una vez la resolución fue verificada o aprobada, el número de
     * participantes y servicios queda fijo (ya se validó contra
     * numero_firmas / numero_servicios), así que se bloquea su edición.
     */
    public function puedeEditarParticipantesServicios(): bool
    {
        return $this->resolucion->auth_status === Resolucion::ESTADO_PENDIENTE;
    }

    public function openCreateParticipante(): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            session()->flash('error', 'No se pueden agregar participantes: la resolución ya fue verificada o aprobada.');
            return;
        }

        $this->resetParticipanteForm();
        $this->showParticipanteModal = true;
    }

    public function openEditParticipante(int $participanteId): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            session()->flash('error', 'No se pueden editar participantes: la resolución ya fue verificada o aprobada.');
            return;
        }

        $participante = $this->resolucion->participantes()->findOrFail($participanteId);


        $this->participanteId = $participante->id;
        $this->participante_user_id = $participante->user_id;
        $this->participante_nombre_firmante = $participante->nombre_firmante;
        $this->participante_documento_identidad = $participante->documento_identidad;
        $this->participante_cargo = $participante->cargo;
        $this->participante_orden_firma = $participante->orden_firma;

        $this->showParticipanteModal = true;
    }

    public function saveParticipante(): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            $this->addError('global', 'No se pueden guardar participantes: la resolución ya fue verificada o aprobada.');
            return;
        }
        $data = $this->validate(
            $this->rulesParticipantes(),
            $this->messagesParticipantes()
        );

        $this->resolucion->participantes()->updateOrCreate(
            ['id' => $this->participanteId],
            [
                'user_id' => $data['participante_user_id'],
                'nombre_firmante' => $data['participante_nombre_firmante'],
                'documento_identidad' => $data['participante_documento_identidad'],
                'cargo' => $data['participante_cargo'],
                'orden_firma' => $data['participante_orden_firma'],
            ]
        );

        $this->showParticipanteModal = false;
        $this->resetParticipanteForm();

        session()->flash('message', 'Participante guardado correctamente.');
    }


    public function openCreateServicio(): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            session()->flash('error', 'No se pueden agregar servicios: la resolución ya fue verificada o aprobada.');
            return;
        }

        $this->resetServicioForm();
        $this->showServicioModal = true;
    }

    public function openEditServicio(int $servicioId): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            session()->flash('error', 'No se pueden editar servicios: la resolución ya fue verificada o aprobada.');
            return;
        }
        $servicio = $this->resolucion->resolucionServicios()->findOrFail($servicioId);

        $this->servicioId = $servicio->id;
        $this->servicio_catalogo_servicio_id = $servicio->catalogo_servicio_id;
        $this->servicio_cantidad = $servicio->cantidad;
        $this->servicio_prioridad = $servicio->prioridad;
        $this->servicio_observaciones = $servicio->observaciones;
        $this->servicio_estado = $servicio->estado;
        $this->servicio_costo_unitario = $servicio->costo_unitario;

        $this->showServicioModal = true;
    }

    public function saveServicio(): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            $this->addError('global', 'No se pueden guardar servicios: la resolución ya fue verificada o aprobada.');
            return;
        }
        $data = $this->validate($this->rulesServicios());

        $this->resolucion->resolucionServicios()->updateOrCreate(
            ['id' => $this->servicioId],
            [
                'catalogo_servicio_id' => $data['servicio_catalogo_servicio_id'],
                'cantidad' => $data['servicio_cantidad'],
                'prioridad' => $data['servicio_prioridad'],
                'observaciones' => $data['servicio_observaciones'],
                'estado' => $data['servicio_estado'],
                'costo_unitario' => $data['servicio_costo_unitario'],
            ]
        );

        $this->showServicioModal = false;
        $this->resetServicioForm();

        session()->flash('message', 'Servicio guardado correctamente.');
    }

    /**
     * @param 'participante'|'servicio' $type
     */
    public function confirmDelete(int $id, string $type): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            session()->flash('error', 'No se pueden eliminar registros: la resolución ya fue verificada o aprobada.');
            return;
        }

        $this->deleteId = $id;
        $this->deleteType = $type;
        $this->confirmingDelete = true;
    }

    /**
     * Reemplaza al antiguo deleteParticipante() que se llamaba sin importar
     * si el registro a borrar era un participante o un servicio.
     */
    public function delete(): void
    {
        if (!$this->puedeEditarParticipantesServicios()) {
            session()->flash('error', 'No se pueden eliminar registros: la resolución ya fue verificada o aprobada.');
            $this->confirmingDelete = false;
            return;
        }
        match ($this->deleteType) {
            'participante' => $this->resolucion->participantes()->findOrFail($this->deleteId)->delete(),
            'servicio' => $this->resolucion->resolucionServicios()->findOrFail($this->deleteId)->delete(),
            default => null,
        };

        $mensaje = $this->deleteType === 'servicio'
            ? 'Servicio eliminado correctamente.'
            : 'Participante eliminado correctamente.';

        $this->confirmingDelete = false;
        $this->deleteId = null;
        $this->deleteType = null;

        session()->flash('message', $mensaje);
    }

    private function resetParticipanteForm(): void
    {
        $this->participanteId = null;
        $this->participante_user_id = null;
        $this->participante_nombre_firmante = null;
        $this->participante_documento_identidad = null;
        $this->participante_cargo = null;
        $this->participante_orden_firma = 1;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    private function resetServicioForm(): void
    {
        $this->servicioId = null;
        $this->servicio_catalogo_servicio_id = null;
        $this->servicio_cantidad = null;
        $this->servicio_prioridad = null;
        $this->servicio_observaciones = null;
        $this->servicio_estado = null;
        $this->servicio_costo_unitario = null;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.operacion.resoluciones.show', [
            'participantes' => $this->resolucion->participantes,
            'servicios'     => $this->resolucion->resolucionServicios,
        ]);
    }
}
