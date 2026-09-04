<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\ContratoServicio;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use App\Livewire\Concerns\ManejaEstadoBloqueado;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\{Auth, DB, Gate, Storage, Log};
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Liquidar extends Component
{
    use ManejaEstadoBloqueado;

    public ContratoServicio $contrato;
    public $observaciones;
    public $acepta_responsabilidad = false;
    public $documento_pdf;

    protected $rules = [
        'acepta_responsabilidad' => 'accepted',
        'observaciones'          => 'required|min:5|string',
        'documento_pdf'          => 'required|file|mimes:pdf|max:5120',
    ];


    public function mount(ContratoServicio $contrato)
    {
        $this->contrato = $contrato;
        $detallesBase = ['Contrato' => $contrato->codigo, 'Estado actual' => $contrato->estadoLabel()];

        // Liquidar es exclusivo de Presidente — Gate ya lo restringe,
        // pero el mensaje de bloqueo debe ser explícito sobre el rol.
        $check = Gate::inspect('contratos-servicios.liquidar', $contrato);
        if (! $check->allowed()) {
            $this->bloquearAcceso($check->message() ?: 'Solo el Presidente barrial puede liquidar un contrato.', route('contratos-servicios.lista'), $detallesBase, 'Sin permisos');
            return;
        }

        if (!$contrato->puedeRescindirseOLiquidarse()) {
            $this->bloquearAcceso('Solo un contrato Aprobado (vigente) puede liquidarse.', route('contratos-servicios.lista'), $detallesBase + ['Estado requerido' => ContratoServicio::ESTADO_APROBADA], 'Estado incorrecto');
            return;
        }
    }


    protected $messages = [
        'documento_pdf.required' => 'Debe adjuntar el acta de entrega-recepción o documento de liquidación.',
    ];

    public function save()
    {
        Gate::authorize('contratos-servicios.liquidar', $this->contrato);

        if (!$this->contrato->puedeRescindirseOLiquidarse()) {
            $this->addError('global', 'Este contrato ya no está en estado Aprobado.');
            return;
        }

        $this->validate();
        $user = Auth::user();
        $path = null;

        try {
            $evento = DB::transaction(function () use ($user, &$path) {
                $directory = 'contratos_servicios/liquidaciones/' . date('Y/m');
                $filename = "{$this->contrato->codigo}-liquidacion.pdf";

                $path = $this->documento_pdf->storeAs($directory, $filename, 'contratos_servicios');
                $fullPath = Storage::disk('contratos_servicios')->path($path);
                $hash = hash_file('sha256', $fullPath);
                $mime = $this->documento_pdf->getMimeType();

                $this->contrato->update([
                    'liquidado_por'               => $user->id,
                    'fecha_liquidacion'           => now(),
                    'documento_liquidacion_path'  => $path,
                    'documento_liquidacion_hash'  => $hash,
                    'documento_liquidacion_mime'  => $mime,
                    'observaciones'               => trim(($this->contrato->observaciones ?? '') . ' | LIQUIDACIÓN: ' . $this->observaciones),
                    'auth_status'                 => ContratoServicio::ESTADO_LIQUIDADO,
                ]);

                return AuditEvent::logEvent(
                    $this->contrato,
                    $user->id,
                    AuditEvent::EVENT_CONTRATO_SERVICIO_LIQUIDADO,
                    ['message' => $this->observaciones, 'documento_hash' => $hash]
                );
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'contrato_servicio_liquidacion', $e);

            if ($path && Storage::disk('contratos_servicios')->exists($path)) {
                Storage::disk('contratos_servicios')->delete($path);
            }

            $this->addError('global', 'Error al liquidar el contrato.');
            return;
        }

        session()->flash('success', 'Contrato liquidado y documento registrado.');
        return redirect()->route('contratos-servicios.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.contratos-servicios.liquidar');
    }
}
