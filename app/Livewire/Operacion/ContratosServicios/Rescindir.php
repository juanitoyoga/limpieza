<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\ContratoServicio;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use App\Livewire\Concerns\ManejaEstadoBloqueado;
use App\Services\LogSistemaService;
use Illuminate\Support\Facades\{Auth, DB, Gate, Storage, Log};
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Rescindir extends Component
{
    use ManejaEstadoBloqueado, WithFileUploads;

    public ContratoServicio $contrato;
    public $observaciones;
    public $acepta_responsabilidad = false;
    public $documento_pdf;

    protected $rules = [
        'acepta_responsabilidad' => 'accepted',
        'observaciones'          => 'required|min:5|string',
        'documento_pdf'          => 'required|file|mimes:pdf|max:5120',
    ];

    protected $messages = [
        'documento_pdf.required' => 'Debe adjuntar el documento que respalda la rescisión.',
    ];

    public function mount(ContratoServicio $contrato)
    {
        $this->contrato = $contrato;
        $detallesBase = ['Contrato' => $contrato->codigo, 'Estado actual' => $contrato->estadoLabel()];

        $check = Gate::inspect('contratos-servicios.rescindir', $contrato);
        if (! $check->allowed()) {
            $this->bloquearAcceso($check->message() ?: 'Solo el Presidente barrial puede rescindir un contrato.', route('contratos-servicios.lista'), $detallesBase, 'Sin permisos');
            return;
        }

        if (!$contrato->puedeRescindirseOLiquidarse()) {
            $this->bloquearAcceso('Solo un contrato Aprobado (vigente) puede rescindirse.', route('contratos-servicios.lista'), $detallesBase + ['Estado requerido' => ContratoServicio::ESTADO_APROBADA], 'Estado incorrecto');
            return;
        }
    }

    public function save()
    {
        Gate::authorize('contratos-servicios.rescindir', $this->contrato);

        if (!$this->contrato->puedeRescindirseOLiquidarse()) {
            $this->addError('global', 'Este contrato ya no está en estado Aprobado.');
            return;
        }

        $this->validate();
        $user = Auth::user();
        $path = null;

        try {
            $evento = DB::transaction(function () use ($user, &$path) {
                // 1. Almacenar el documento y calcular su hash — mismo
                //    patrón que Resolucion::Create para el documento original.
                $directory = 'contratos_servicios/rescisiones/' . date('Y/m');
                $filename = "{$this->contrato->codigo}-rescision.pdf";

                $path = $this->documento_pdf->storeAs($directory, $filename, 'contratos_servicios');
                $fullPath = Storage::disk('contratos_servicios')->path($path);
                $hash = hash_file('sha256', $fullPath);
                $mime = $this->documento_pdf->getMimeType();

                $this->contrato->update([
                    'rescindido_por'             => $user->id,
                    'fecha_rescision'            => now(),
                    'documento_rescision_path'   => $path,
                    'documento_rescision_hash'   => $hash,
                    'documento_rescision_mime'   => $mime,
                    'observaciones'              => trim(($this->contrato->observaciones ?? '') . ' | RESCISIÓN: ' . $this->observaciones),
                    'auth_status'                => ContratoServicio::ESTADO_RESCINDIDO,
                ]);

                return AuditEvent::logEvent(
                    $this->contrato,
                    $user->id,
                    AuditEvent::EVENT_CONTRATO_SERVICIO_RESCINDIDO,
                    ['message' => $this->observaciones, 'documento_hash' => $hash]
                );
            });

            DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
        } catch (\Throwable $e) {
            LogSistemaService::registrarExcepcion(static::class, 'contrato_servicio_rescision', $e);

            if ($path && Storage::disk('contratos_servicios')->exists($path)) {
                Storage::disk('contratos_servicios')->delete($path);
            }

            $this->addError('global', 'Error al rescindir el contrato.');
            return;
        }

        session()->flash('success', 'Contrato rescindido y documento registrado.');
        return redirect()->route('contratos-servicios.lista');
    }

    public function render()
    {
        return $this->renderBloqueadoOr('livewire.operacion.contratos-servicios.rescindir');
    }
}
