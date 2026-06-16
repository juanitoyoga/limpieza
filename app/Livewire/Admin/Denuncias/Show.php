<?php

namespace App\Livewire\Admin\Denuncias;

use Livewire\Component;
use App\Models\Denuncia;
use Livewire\Attributes\Layout;
use App\Models\Multa;
use App\Models\PorcentajeMultas;
use App\Models\SalarioMinimo;
use App\Models\Contrato;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;

#[Layout('layouts.admin')]

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
    public $motivo_rechazo;

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

    protected function verificarRolPermitido()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role_name, ['Funcionario', 'Supervisor'])) {
            session()->flash('error', 'Acceso denegado. Se requiere rol de Funcionario o Supervisor.');
            return false;
        }
        return true;
    }

    // ───────────────────────────────────────────────
    // ACCIÓN: APROBAR Y GENERAR MULTA MULTI-NIVEL
    // ───────────────────────────────────────────────
    public function aprobarDenuncia()
    {
        if (!$this->verificarRolPermitido()) return;

        if ($this->multa_calculada <= 0) {
            session()->flash('error', 'No se puede aprobar la denuncia porque el cálculo de la multa es 0 o no está parametrizado.');
            return;
        }

        $user = Auth::user();
        $rolOperador = $user->role_name;

        // Recuperar el contrato vigente del barrio para extraer la matriz de distribución económica
        $contratoBarrio = Contrato::where('barrio_id', $this->denuncia->barrio_id)
            ->where('estado', Contrato::ESTADO_APROBADO) // o ESTADO_FINALIZADO dependiendo de tu regla
            ->first();

        // Valores por defecto si el barrio no tiene contrato aún firmado
        $pBarrio = $contratoBarrio ? $contratoBarrio->porcentaje_barrio : 40.00;
        $pMunicipio = $contratoBarrio ? $contratoBarrio->porcentaje_dmq : 40.00;
        $pPlataforma = $contratoBarrio ? $contratoBarrio->porcentaje_ltr : 20.00;

        // DB Transaction para asegurar consistencia absoluta (O se hace todo o nada)
        DB::transaction(function () use ($user, $rolOperador, $pBarrio, $pMunicipio, $pPlataforma) {

            // 1. Actualizar Denuncia core
            $this->denuncia->update([
                'estado'          => 'Aprobado',
                'aprobado_por_id'  => $user->id,
                'aprobado_por_rol' => $rolOperador,
                'aprobado_at'      => now(),
                'multa_calculada'  => $this->multa_calculada,
            ]);

            // 2. Generar el registro financiero de la Multa con distribución algorítmica
            Multa::create([
                'denuncia_id'           => $this->denuncia->id,
                'ordenanza332_id'       => $this->denuncia->ordenanza332_id,
                'vecino_id'             => $this->denuncia->vecino_id,
                'funcionario_id'        => $user->id, // Funcionario/Supervisor que ejecuta
                'barrio_id'             => $this->denuncia->barrio_id,

                // Identificadores Administrativos únicos
                'codigo_unico'          => 'MUL-' . strtoupper(Str::random(8)),
                'numero_expediente'     => 'EXP-' . $this->denuncia->id . '-' . date('Y'),
                'numero_resolucion'     => 'RES-' . rand(1000, 9999),

                // Base Financiera
                'porcentaje_salario'    => $this->porcentaje_infraccion,
                'salario_base'          => $this->salario_vigente,
                'valor_multa'           => $this->multa_calculada,

                // Distribución Proporcional de Ingresos
                'porcentaje_barrio'     => $pBarrio,
                'valor_barrio'          => $this->multa_calculada * ($pBarrio / 100),

                'porcentaje_municipio'  => $pMunicipio,
                'valor_municipio'       => $this->multa_calculada * ($pMunicipio / 100),

                'porcentaje_plataforma' => $pPlataforma,
                'valor_plataforma'      => $this->multa_calculada * ($pPlataforma / 100),

                // Estado y Ciclo de vida
                'estado'                => 'pendiente',
                'fecha_emision'         => now(),
                'fecha_vencimiento'     => now()->addDays(15), // 15 días reglamentarios para pagar
            ]);
        });

        $this->mostrarModalAprobar = false;
        $this->cargarDenuncia($this->denuncia->id);
        session()->flash('message', 'Denuncia aprobada de forma exitosa. Se ha calculado la sanción y distribuido los dividendos económicos en el sistema.');
    }

    // Los métodos verificarDenuncia() y rechazarDenuncia() se mantienen iguales...

    public function verificarDenuncia()
    {
        if (!$this->verificarRolPermitido()) return;
        $this->denuncia->update([
            'estado' => 'Verificado',
            'verificado_por_id' => Auth::user()->id,
            'verificado_por_rol' => Auth::user()->role_name,
            'verificado_at' => now(),
        ]);
        $this->mostrarModalVerificar = false;
        $this->cargarDenuncia($this->denuncia->id);
        session()->flash('message', 'Denuncia marcada como Verificada.');
    }

    public function rechazarDenuncia()
    {
        if (!$this->verificarRolPermitido()) return;
        $this->validate(['motivo_rechazo' => 'required|string|min:10']);
        $this->denuncia->update([
            'estado' => 'Rechazado',
            'rechazado_por_id' => Auth::user()->id,
            'rechazado_por_rol' => Auth::user()->role_name,
            'rechazado_at' => now(),
            'motivo_rechazo' => $this->motivo_rechazo,
        ]);
        $this->mostrarModalRechazar = false;
        $this->cargarDenuncia($this->denuncia->id);
        session()->flash('message', 'Denuncia desestimada.');
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

        return view('livewire.admin.denuncias.show');
    }
}
