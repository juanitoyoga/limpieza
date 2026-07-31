<?php

namespace App\Livewire\Operacion\Denuncias;

use Livewire\Component;
use App\Models\Denuncia;
use Livewire\Attributes\Layout;
use App\Models\Multa;
use App\Models\PorcentajeMultas;
use App\Models\SalarioMinimo;
use App\Models\Contrato;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('layouts.operacion')]

class Show extends Component
{
    public $denuncia;

    // Nombres procesados dinámicamente
    public $verificado_nombre = null;
    public $aprobado_nombre = null;
    public $rechazado_nombre = null;

    // Control de Modales
    public $mostrarModalVerificar = false;
    public $mostrarModalAprobar = false;
    public $mostrarModalRechazar = false;

    // Propiedades autocalculadas para mostrar en el modal de aprobación
    public $salario_vigente = 0;
    public $porcentaje_infraccion = 0;
    public $multa_calculada = 0;
    public $motivo_rechazo = '  ';
    public $pBarrio     = 40.00;
    public $pMunicipio  = 40.00;
    public $pPlataforma = 20.00;

    public function mount($id)
    {
        $this->cargarDenuncia($id);
    }

    public function cargarDenuncia($id)
    {
        $this->denuncia = Denuncia::with([
            'vecino.user',
            'barrio',
            'ordenanza332',
            'verificadoPorFuncionario.user',
            'verificadoPorSupervisor.user',
            'aprobadoPorFuncionario.user',
            'aprobadoPorSupervisor.user',
            'rechazadoPorFuncionario.user',
            'rechazadoPorSupervisor.user'
        ])->findOrFail($id);

        $this->resolverNombresAuditoria();
        $this->prepararCalculosMulta();
    }

    /**
     * Precalcula los valores en base a la ordenanza y salario mínimo vigente
     * para que el funcionario los visualice de forma transparente en el modal.
     */
    public function prepararCalculosMulta()
    {

        $contrato = Contrato::where('barrio_id', $this->denuncia->barrio_id)
            ->where('estado', Contrato::ESTADO_APROBADO)
            ->latest()->first();

        $this->pBarrio     = $contrato?->porcentaje_barrio ?? 40.00;
        $this->pMunicipio  = $contrato?->porcentaje_dmq    ?? 40.00;
        $this->pPlataforma = $contrato?->porcentaje_ltr    ?? 20.00;

        $salario = SalarioMinimo::vigente();
        if (!$salario) return;

        $this->salario_vigente = $salario->valor_usd;

        $parametrizacion = PorcentajeMultas::where('ordenanza332_id', $this->denuncia->ordenanza332_id)
            ->where('salariominimo_id', $salario->id)
            ->first();

        if ($parametrizacion) {
            $this->porcentaje_infraccion = $parametrizacion->porcentaje;
            // Ejecutamos el método matemático de tu modelo PorcentajeMultas
            $this->multa_calculada = $parametrizacion->calcularMulta();
        }
    }

    protected function verificarRolPermitido(array $roles = ['Funcionario', 'Supervisor']): bool
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_name, $roles)) {
            session()->flash('error', 'Acceso denegado. No tienes permiso para esta acción.');
            return false;
        }
        return true;
    }

    public function verificarDenuncia()
    {
        if (!$this->verificarRolPermitido(['Funcionario'])) return;

        if ($this->denuncia->estado !== Denuncia::ESTADO_PENDIENTE) {
            session()->flash('error', 'Solo se pueden verificar denuncias pendientes.');
            return;
        }

        $user        = Auth::user();
        $funcionario = \App\Models\Funcionario::where('user_id', $user->id)->first()
            ?? \App\Models\Supervisor::where('user_id', $user->id)->first();

        if (!$funcionario) {
            session()->flash('error', 'No se encontró el perfil del operador.');
            return;
        }

        $this->denuncia->update([
            'estado'             => Denuncia::ESTADO_VERIFICADA,
            'verificado_por_id'  => $funcionario->id,
            'verificado_por_rol' => strtolower($user->role_name),
            'verificado_at'      => now(),
        ]);

        $auditEvent = \App\Models\AuditEvent::logEvent(
            $this->denuncia,
            $user->id,
            'denuncia_verificada',
            ['verificado_por' => $funcionario->id]
        );
        \App\Jobs\RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        $this->mostrarModalVerificar = false;
        $this->cargarDenuncia($this->denuncia->id);
        session()->flash('message', 'Denuncia verificada correctamente.');
    }

    public function aprobarDenuncia()
    {
        // Solo Supervisor puede aprobar
        if (!$this->verificarRolPermitido(['Supervisor'])) return;

        if ($this->denuncia->estado !== Denuncia::ESTADO_VERIFICADA) {
            session()->flash('error', 'Solo se pueden aprobar denuncias verificadas.');
            return;
        }

        if ($this->multa_calculada <= 0) {
            session()->flash('error', 'No se puede aprobar: multa no parametrizada.');
            return;
        }

        $user       = Auth::user();
        $supervisor = \App\Models\Supervisor::where('user_id', $user->id)->firstOrFail();

        $contratoBarrio = Contrato::where('barrio_id', $this->denuncia->barrio_id)
            ->where('estado', Contrato::ESTADO_APROBADO)
            ->latest()
            ->first();

        $pBarrio     = $contratoBarrio?->porcentaje_barrio ?? 40.00;
        $pMunicipio  = $contratoBarrio?->porcentaje_dmq    ?? 40.00;
        $pPlataforma = $contratoBarrio?->porcentaje_ltr    ?? 20.00;

        DB::transaction(function () use ($user, $supervisor, $pBarrio, $pMunicipio, $pPlataforma) {

            $this->denuncia->update([
                'estado'           => Denuncia::ESTADO_APROBADA,
                'aprobado_por_id'  => $supervisor->id,
                'aprobado_por_rol' => 'supervisor',
                'aprobado_at'      => now(),
                'multa_calculada'  => $this->multa_calculada,
            ]);

            $multa = Multa::create([
                'denuncia_id'           => $this->denuncia->id,
                'ordenanza332_id'       => $this->denuncia->ordenanza332_id,
                'vecino_id'             => $this->denuncia->vecino_id,
                'supervisor_id'         => $supervisor->id,
                'barrio_id'             => $this->denuncia->barrio_id,
                'codigo_unico'          => 'MUL-' . strtoupper(Str::random(8)),
                'numero_expediente'     => 'EXP-' . $this->denuncia->id . '-' . date('Y'),
                'numero_resolucion'     => 'RES-' . rand(1000, 9999),
                'porcentaje_salario'    => $this->porcentaje_infraccion,
                'salario_base'          => $this->salario_vigente,
                'valor_multa'           => $this->multa_calculada,
                'porcentaje_barrio'     => $pBarrio,
                'valor_barrio'          => round($this->multa_calculada * ($pBarrio    / 100), 2),
                'porcentaje_municipio'  => $pMunicipio,
                'valor_municipio'       => round($this->multa_calculada * ($pMunicipio / 100), 2),
                'porcentaje_plataforma' => $pPlataforma,
                'valor_plataforma'      => round($this->multa_calculada * ($pPlataforma / 100), 2),
                'estado'                => 'Pendiente',
                'fecha_emision'         => now(),
                'fecha_vencimiento'     => now()->addDays(15),
            ]);

            $auditDenuncia = \App\Models\AuditEvent::logEvent(
                $this->denuncia,
                $user->id,
                'denuncia_aprobada',
                ['aprobado_por' => $supervisor->id, 'multa_id' => $multa->id]
            );
            \App\Jobs\RegistrarEventoBlockchain::dispatch($auditDenuncia->id)->onQueue('blockchain');

            $auditMulta = \App\Models\AuditEvent::logEvent(
                $multa,
                $user->id,
                'multa_emitida',
                ['valor_multa' => $this->multa_calculada]
            );
            \App\Jobs\RegistrarEventoBlockchain::dispatch($auditMulta->id)->onQueue('blockchain');
        });

        $this->mostrarModalAprobar = false;
        $this->cargarDenuncia($this->denuncia->id);
        session()->flash('message', 'Denuncia aprobada y multa emitida correctamente.');
    }

    public function rechazarDenuncia()
    {
        if (!$this->verificarRolPermitido(['Funcionario', 'Supervisor'])) return;
        $this->validate(['motivo_rechazo' => 'required|string|min:10']);

        if (!in_array($this->denuncia->estado, [Denuncia::ESTADO_PENDIENTE, Denuncia::ESTADO_VERIFICADA])) {
            session()->flash('error', 'Esta denuncia no puede ser rechazada en su estado actual.');
            return;
        }

        $user        = Auth::user();
        $funcionario = \App\Models\Funcionario::where('user_id', $user->id)->first()
            ?? \App\Models\Supervisor::where('user_id', $user->id)->first();

        if (!$funcionario) {
            session()->flash('error', 'No se encontró el perfil del operador.');
            return;
        }

        $this->denuncia->update([
            'estado'            => Denuncia::ESTADO_RECHAZADA,
            'rechazado_por_id'  => $funcionario->id,
            'rechazado_por_rol' => 'supervisor',
            'rechazado_at'      => now(),
            'motivo_rechazo'    => $this->motivo_rechazo,
        ]);

        $auditEvent = \App\Models\AuditEvent::logEvent(
            $this->denuncia,
            $user->id,
            'denuncia_rechazada',
            ['motivo' => $this->motivo_rechazo]
        );
        \App\Jobs\RegistrarEventoBlockchain::dispatch($auditEvent->id)->onQueue('blockchain');

        $this->mostrarModalRechazar = false;
        $this->cargarDenuncia($this->denuncia->id);
        session()->flash('message', 'Denuncia rechazada.');
    }
    protected function resolverNombresAuditoria()
    {
        foreach (['verificado', 'aprobado', 'rechazado'] as $accion) {
            $rolField = $accion . '_por_rol';
            if ($this->denuncia->$rolField) {
                $rel = $accion . 'Por' . $this->denuncia->$rolField;
                $u = $this->denuncia->$rel?->user;
                $prop = $accion . '_nombre';
                $this->$prop = $u ? $u->first_name . ' ' . $u->last_name : 'No encontrado';
            }
        }
    }

    public function render()
    {

        return view('livewire.operacion.denuncias.show');
    }
}
