<?php

namespace App\Livewire\Operacion\Notificaciones;

use Livewire\Component;
use App\Models\Notificacion;
use App\Models\Denuncia;
use App\Models\Funcionario;
use App\Models\Supervisor;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Show extends Component
{
    public $notificacion;

    public $verificado_nombre = null;
    public $aprobado_nombre = null;
    public $rechazado_nombre = null;

    public $mostrarModalVerificar = false;
    public $mostrarModalAprobar = false;
    public $mostrarModalRechazar = false;

    public $motivo_rechazo = '';

    public function mount($id)
    {
        $this->cargarNotificacion($id);
    }

    public function cargarNotificacion($id)
    {
        $this->notificacion = Notificacion::with([
            'denuncia',
            'user',
            'barrio',
            'ordenanza332',
            'barrioAtributo',
            'verificadoPorFuncionario.user',
            'verificadoPorSupervisor.user',
            'aprobadoPorFuncionario.user',
            'aprobadoPorSupervisor.user',
            'rechazadoPorFuncionario.user',
            'rechazadoPorSupervisor.user',
        ])->findOrFail($id);

        $this->resolverNombresAuditoria();
    }

    protected function verificarRolPermitido(array $roles): bool
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_name, $roles)) {
            session()->flash('error', 'Acceso denegado. No tienes permiso para esta acción.');
            return false;
        }
        return true;
    }

    // ───────────────────────────────────────────────
    // VERIFICAR — solo Funcionario
    // ───────────────────────────────────────────────
    public function verificarNotificacion()
    {
        if (!$this->verificarRolPermitido(['Funcionario'])) return;

        if ($this->notificacion->estado !== Notificacion::ESTADO_ENVIADA) {
            session()->flash('error', 'Solo se pueden verificar notificaciones con evidencia enviada.');
            return;
        }

        $user        = Auth::user();
        $funcionario = Funcionario::where('user_id', $user->id)->firstOrFail();

        $this->notificacion->update([
            'estado'             => Notificacion::ESTADO_VERIFICADA,
            'verificado_por_id'  => $funcionario->id,
            'verificado_por_rol' => 'Funcionario',
            'verificado_at'      => now(),
        ]);

        $auditEvent = AuditEvent::logEvent(
            $this->notificacion,
            $user->id,
            'notificacion_verificada',
            ['verificado_por' => $funcionario->id]
        );
        RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        $this->mostrarModalVerificar = false;
        $this->cargarNotificacion($this->notificacion->id);
        session()->flash('message', 'Notificación verificada correctamente.');
    }

    // ───────────────────────────────────────────────
    // APROBAR — solo Supervisor, cierra la Denuncia
    // ───────────────────────────────────────────────
    public function aprobarNotificacion()
    {
        if (!$this->verificarRolPermitido(['Supervisor'])) return;

        if ($this->notificacion->estado !== Notificacion::ESTADO_VERIFICADA) {
            session()->flash('error', 'Solo se pueden aprobar notificaciones verificadas.');
            return;
        }

        $user       = Auth::user();
        $supervisor = Supervisor::where('user_id', $user->id)->firstOrFail();

        DB::transaction(function () use ($user, $supervisor) {

            $this->notificacion->update([
                'estado'           => Notificacion::ESTADO_APROBADA,
                'aprobado_por_id'  => $supervisor->id,
                'aprobado_por_rol' => 'Supervisor',
                'aprobado_at'      => now(),
            ]);

            $denuncia = $this->notificacion->denuncia;
            $denuncia->update(['estado' => Denuncia::ESTADO_CERRADA]);

            $auditNotificacion = AuditEvent::logEvent(
                $this->notificacion,
                $user->id,
                'notificacion_aprobada',
                ['aprobado_por' => $supervisor->id, 'denuncia_id' => $denuncia->id]
            );
            RegistrarEventoBlockchain::dispatch($auditNotificacion->id)->onQueue('blockchain');

            $auditDenuncia = AuditEvent::logEvent(
                $denuncia,
                $user->id,
                'denuncia_cerrada_por_justificacion',
                ['notificacion_id' => $this->notificacion->id, 'aprobado_por' => $supervisor->id]
            );
            RegistrarEventoBlockchain::dispatch($auditDenuncia->id)->onQueue('blockchain');
        });

        $this->mostrarModalAprobar = false;
        $this->cargarNotificacion($this->notificacion->id);
        session()->flash('message', 'Notificación aprobada. La denuncia queda cerrada.');
    }

    // ───────────────────────────────────────────────
    // RECHAZAR — Funcionario o Supervisor, reabre la Denuncia
    // ───────────────────────────────────────────────
    public function rechazarNotificacion()
    {
        if (!$this->verificarRolPermitido(['Funcionario', 'Supervisor'])) return;
        $this->validate(['motivo_rechazo' => 'required|string|min:10']);

        if (!in_array($this->notificacion->estado, [Notificacion::ESTADO_ENVIADA, Notificacion::ESTADO_VERIFICADA])) {
            session()->flash('error', 'Esta notificación no puede ser rechazada en su estado actual.');
            return;
        }

        $user    = Auth::user();
        $revisor = Funcionario::where('user_id', $user->id)->first()
            ?? Supervisor::where('user_id', $user->id)->first();

        if (!$revisor) {
            session()->flash('error', 'No se encontró el perfil del operador.');
            return;
        }

        DB::transaction(function () use ($user, $revisor) {

            $this->notificacion->update([
                'estado'            => Notificacion::ESTADO_RECHAZADA,
                'rechazado_por_id'  => $revisor->id,
                'rechazado_por_rol' => $user->role_name,
                'rechazado_at'      => now(),
                'motivo_rechazo'    => $this->motivo_rechazo,
            ]);

            $denuncia = $this->notificacion->denuncia;
            $denuncia->update(['estado' => Denuncia::ESTADO_PENDIENTE]);

            $auditNotificacion = AuditEvent::logEvent(
                $this->notificacion,
                $user->id,
                'notificacion_rechazada',
                ['motivo' => $this->motivo_rechazo, 'denuncia_id' => $denuncia->id]
            );
            RegistrarEventoBlockchain::dispatch($auditNotificacion->id)->onQueue('blockchain');

            $auditDenuncia = AuditEvent::logEvent(
                $denuncia,
                $user->id,
                'denuncia_reabierta_por_rechazo_notificacion',
                ['notificacion_id' => $this->notificacion->id, 'motivo' => $this->motivo_rechazo]
            );
            RegistrarEventoBlockchain::dispatch($auditDenuncia->id)->onQueue('blockchain');
        });

        $this->mostrarModalRechazar = false;
        $this->cargarNotificacion($this->notificacion->id);
        session()->flash('message', 'Notificación rechazada. La denuncia vuelve a estado pendiente.');
    }

    protected function resolverNombresAuditoria()
    {
        foreach (['verificado', 'aprobado', 'rechazado'] as $accion) {
            $rolField = $accion . '_por_rol';
            if ($this->notificacion->$rolField) {
                $rel  = $accion . 'Por' . $this->notificacion->$rolField;
                $u    = $this->notificacion->$rel?->user;
                $prop = $accion . '_nombre';
                $this->$prop = $u ? $u->first_name . ' ' . $u->last_name : 'No encontrado';
            }
        }
    }

    public function render()
    {
        return view('livewire.operacion.notificaciones.show');
    }
}
