<?php

namespace App\Livewire\Operacion\Ofertas;

use App\Models\Oferta;
use App\Models\Proveedor;
use App\Models\Resolucion;
use App\Models\AuditEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Jobs\RegistrarEventoBlockchain;

#[Layout('layouts.operacion')]
class Create extends Component
{
    use WithFileUploads;

    public $codigo;
    public $proveedor_id;
    public $resolucion_id;
    public $titulo;
    public $descripcion;
    public $fecha_presentacion;
    public $documento_pdf;
    public $evento_json;
    public $auth_status = 'Pendiente';

    public $proveedores = [];
    public $resoluciones = [];

    protected $rules = [
        'codigo'           => 'required|string|max:255|unique:ofertas,codigo',
        'proveedor_id'     => 'required|exists:proveedores,id',
        'resolucion_id'    => 'required|exists:resoluciones,id',
        'titulo'           => 'required|string|max:255',
        'descripcion'      => 'nullable|string',
        'fecha_presentacion' => 'required|date',
        'documento_pdf'    => 'required|file|mimes:pdf|max:5120',
        'evento_json'      => 'nullable|json',
    ];

    public function mount()
    {
        Gate::authorize('ofertas.create');

        $this->proveedores = Proveedor::orderBy('nombre')->get();
        $this->resoluciones = Resolucion::orderBy('codigo')->get();
    }

    public function save()
    {
        $this->validate();

        $path = null;

        try {
            $oferta = DB::transaction(function () use (&$path) {
                $userId = Auth::id();

                $oferta = Oferta::create([
                    'codigo'           => $this->codigo,
                    'proveedor_id'     => $this->proveedor_id,
                    'resolucion_id'    => $this->resolucion_id,
                    'titulo'           => $this->titulo,
                    'descripcion'      => $this->descripcion,
                    'fecha_presentacion' => $this->fecha_presentacion,
                    'evento_json'      => $this->evento_json ? json_decode($this->evento_json, true) : null,
                    'auth_status'      => $this->auth_status,
                ]);

                // Documento PDF
                $directory = 'ofertas/' . date('Y/m');
                $filename = "{$oferta->codigo}.pdf";

                $path = $this->documento_pdf->storeAs($directory, $filename, 'ofertas');
                $fullPath = Storage::disk('ofertas')->path($path);
                $hash = hash_file('sha256', $fullPath);
                $mime = $this->documento_pdf->getMimeType();

                $oferta->update([
                    'documento_original_path' => $path,
                    'documento_original_hash' => $hash,
                    'documento_original_mime' => $mime,
                ]);

                $this->logOfertaEvents($oferta, $userId, $path, $hash);

                return $oferta;
            });

            session()->flash('message', 'Oferta creada, documentada y auditada correctamente.');

            return redirect()->route('ofertas.lista');
        } catch (\Exception $e) {
            Log::error("Error registrando oferta: " . $e->getMessage());

            if ($path && Storage::disk('ofertas')->exists($path)) {
                Storage::disk('ofertas')->delete($path);
            }

            $this->addError('global', 'Error crítico al procesar la oferta. Intente nuevamente.');
        }
    }

    private function logOfertaEvents(Oferta $oferta, ?int $userId, string $path, string $hash): void
    {
        $auditCreacion = AuditEvent::logEvent(
            $oferta,
            $userId,
            'oferta_creada',
            [
                'codigo' => $oferta->codigo,
                'proveedor' => $oferta->proveedor_id,
                'resolucion' => $oferta->resolucion_id,
            ]
        );

        $auditDocumento = AuditEvent::logEvent(
            $oferta,
            $userId,
            'oferta_documento_subido',
            [
                'path' => $path,
                'hash' => $hash,
            ]
        );

        DB::afterCommit(function () use ($auditCreacion, $auditDocumento) {
            RegistrarEventoBlockchain::dispatch($auditCreacion->id);
            RegistrarEventoBlockchain::dispatch($auditDocumento->id);
        });
    }

    public function render()
    {
        return view('livewire.operacion.ofertas.create');
    }
}
