<?php

namespace App\Livewire\Operacion\ContratosServicios;

use App\Models\{ContratoServicio, ContratoServicioDetalle, Oferta, Resolucion, AuditEvent, ContratoFormaPago};
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate, Storage, Log};
use Livewire\Component;
use Livewire\Attributes\{Layout, Computed};
use Livewire\WithFileUploads;

#[Layout('layouts.operacion')]
class Create extends Component
{
    use WithFileUploads;

    // --- Fase 1: búsqueda del expediente ---
    public string $codigoResolucion = '';
    public string $codigoOferta = '';

    // Null mientras no se ha encontrado/validado una oferta.
    // Una vez seteado, el componente pasa a mostrar el formulario del contrato.
    public ?int $ofertaId = null;

    // --- Fase 2: datos del contrato ---
    public $codigo = '';
    public $titulo = '';
    public $descripcion = '';
    public $fecha_inicio = '';
    public $fecha_fin_estimada = '';
    public $documento_pdf;

    public function mount(): void
    {
        Gate::authorize('contratos-servicios.buscar');
    }

    protected function rules(): array
    {
        if (is_null($this->ofertaId)) {
            return [
                'codigoResolucion' => 'required|string',
                'codigoOferta'     => 'required|string',
            ];
        }

        return [
            'codigo'             => 'required|string|max:255|unique:contratos_servicios,codigo',
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'required|string',
            'fecha_inicio'       => 'required|date',
            'fecha_fin_estimada' => 'required|date|after_or_equal:fecha_inicio',
            'documento_pdf'      => 'required|file|mimes:pdf|max:5120',
        ];
    }

    /**
     * Fase 1 — localizar y validar el expediente. Solo guarda el ID de
     * la oferta como estado del componente; el modelo completo con
     * relaciones nunca se guarda como propiedad pública, se resuelve
     * bajo demanda vía la propiedad computada oferta().
     */
    public function buscar(): void
    {
        $this->validate([
            'codigoResolucion' => 'required|string',
            'codigoOferta'     => 'required|string',
        ]);

        $resolucion = Resolucion::where('codigo', trim($this->codigoResolucion))->first();

        if (! $resolucion) {
            $this->addError('codigoResolucion', "No existe ninguna resolución con el código '{$this->codigoResolucion}'.");
            return;
        }

        if ($resolucion->auth_status !== Resolucion::ESTADO_APROBADA) {
            $this->addError('codigoResolucion', "La resolución está en estado '{$resolucion->estadoLabel()}', debe estar Aprobada.");
            return;
        }

        $oferta = Oferta::where('codigo', trim($this->codigoOferta))->first();

        if (! $oferta) {
            $this->addError('codigoOferta', "No existe ninguna oferta con el código '{$this->codigoOferta}'.");
            return;
        }

        if ($oferta->resolucion_id !== $resolucion->id) {
            $this->addError('codigoOferta', "La oferta '{$oferta->codigo}' no pertenece a la resolución '{$resolucion->codigo}'.");
            return;
        }

        if ($oferta->auth_status !== Oferta::ESTADO_APROBADA) {
            $this->addError('codigoOferta', "La oferta está en estado '{$oferta->estadoLabel()}', debe estar Aprobada.");
            return;
        }

        if (ContratoServicio::where('oferta_id', $oferta->id)->exists()) {
            $this->addError('codigoOferta', "Ya existe un contrato generado para la oferta '{$oferta->codigo}'.");
            return;
        }

        if (Gate::denies('contratos-servicios.crear', $oferta)) {
            $this->addError('codigoOferta', 'No tienes permiso para generar un contrato desde esta oferta.');
            return;
        }

        // Se encontró y validó — pasamos a la fase 2, precargando el formulario.
        $this->ofertaId = $oferta->id;
        $this->titulo = 'Contrato de servicio — ' . $oferta->titulo;
        $this->fecha_inicio = now()->toDateString();
    }

    /**
     * Permite volver a la fase de búsqueda sin recargar la página,
     * por si el usuario se equivocó de expediente.
     */
    public function cambiarExpediente(): void
    {
        $this->reset(['ofertaId', 'codigo', 'titulo', 'descripcion', 'fecha_inicio', 'fecha_fin_estimada', 'documento_pdf']);
        $this->resetErrorBag();
    }

    #[Computed]
    public function oferta()
    {
        if (is_null($this->ofertaId)) {
            return null;
        }

        return Oferta::with(['proveedor', 'ofertaServicios.catalogoServicio', 'formaPago.catalogoServicio'])
            ->findOrFail($this->ofertaId);
    }

    /**
     * Fase 2 — genera el contrato. Solo se llama cuando ofertaId ya
     * está resuelto (el formulario de esta fase no se muestra antes).
     */
    public function save()
    {
        $this->validate();

        $userId = Auth::id();
        $path = null;
        $oferta = $this->oferta;

        try {
            $contrato = DB::transaction(function () use ($userId, &$path, $oferta) {

                $montoCalculado = $oferta->monto_total ?? $oferta->ofertaServicios->sum('subtotal');

                $contrato = ContratoServicio::create([
                    'oferta_id'          => $oferta->id,
                    'proveedor_id'       => $oferta->proveedor_id,
                    'codigo'             => $this->codigo,
                    'titulo'             => $this->titulo,
                    'descripcion'        => $this->descripcion,
                    'fecha_inicio'       => $this->fecha_inicio,
                    'fecha_fin_estimada' => $this->fecha_fin_estimada,
                    'monto_total'        => $montoCalculado,
                    'auth_status'        => ContratoServicio::ESTADO_PENDIENTE,
                ]);

                $directory = 'contratos_servicios/originales/' . date('Y/m');
                $filename = "{$contrato->codigo}.pdf";

                $path = $this->documento_pdf->storeAs($directory, $filename, 'contratos_servicios');

                $fullPath = Storage::disk('contratos_servicios')->path($path);
                $hash = hash_file('sha256', $fullPath);
                $mime = $this->documento_pdf->getMimeType();

                $contrato->update([
                    'documento_original_path' => $path,
                    'documento_original_hash' => $hash,
                    'documento_original_mime' => $mime,
                ]);

                foreach ($oferta->ofertaServicios as $linea) {
                    ContratoServicioDetalle::create([
                        'contrato_servicio_id' => $contrato->id,
                        'catalogo_servicio_id' => $linea->catalogo_servicio_id,
                        'cantidad'             => $linea->cantidad,
                        'costo_unitario'       => $linea->costo_unitario,
                    ]);
                }
                foreach ($oferta->formaPago as $lineaPago) {
                    ContratoFormaPago::create([
                        'contrato_servicio_id' => $contrato->id,
                        'orden'                => $lineaPago->orden,
                        'tipo'                 => $lineaPago->tipo,
                        'catalogo_servicio_id' => $lineaPago->catalogo_servicio_id,
                        'tipo_valor'           => $lineaPago->tipo_valor,
                        'valor'                => $lineaPago->valor,
                        'descripcion'          => $lineaPago->descripcion,
                    ]);
                }
                $evento = AuditEvent::logEvent(
                    $contrato,
                    $userId,
                    'contrato_servicio_creado',
                    ['codigo' => $contrato->codigo, 'oferta_id' => $oferta->id, 'documento_hash' => $hash]
                );

                DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));

                return $contrato;
            });
        } catch (\Throwable $e) {
            Log::error('[ContratosServicios\Create] Error', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
            ]);

            if ($path && Storage::disk('contratos_servicios')->exists($path)) {
                Storage::disk('contratos_servicios')->delete($path);
            }

            $this->addError('global', 'Error al generar el contrato. Intente nuevamente.');
            return;
        }

        session()->flash('message', 'Contrato generado correctamente.');
        return redirect()->route('contratos-servicios.show', $contrato->id);
    }

    public function render()
    {
        return view('livewire.operacion.contratos-servicios.create');
    }
}
