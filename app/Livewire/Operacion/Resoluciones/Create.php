<?php

namespace App\Livewire\Operacion\Resoluciones;

use App\Models\{Resolucion, AuditEvent, Barrio, ServiceType};

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
    public $barrio_id;
    public $titulo;
    public $descripcion;
    public $service_type_id;
    public $fecha_resolucion;
    public $documento_pdf;
    public $numero_firmas;
    public $numero_servicios;
    public $evento_json;
    public $auth_status = 'Pendiente';

    // Se puebla en mount() para alimentar el dropdown de barrios.
    public $barrios = [];

    // Se puebla en mount() para alimentar el dropdown de tipos de servicio.
    public $service_types = [];

    protected $rules = [
        'codigo'           => 'required|string|max:255|unique:resoluciones,codigo',
        'barrio_id'        => 'required|exists:barrios,id',
        'titulo'           => 'required|string|max:255',
        'descripcion'      => 'nullable|string',
        'service_type_id'    => 'required|exists:service_types,id',
        'fecha_resolucion' => 'required|date',
        'documento_pdf'    => 'required|file|mimes:pdf|max:5120',
        'numero_firmas'    => 'required|integer',
        'numero_servicios' => 'required|integer',
        'evento_json'      => 'nullable|json',

    ];

    public function mount()
    {
        Gate::authorize('resoluciones.create');

        $this->barrios = Barrio::orderBy('nombre')->get();
        $this->service_types = ServiceType::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        $path = null;

        try {
            $resolucion = DB::transaction(function () use (&$path) {
                $userId = Auth::id();

                // 1. Crear el registro base de la resolución
                //    (tx_hash y tx_block los completa RegistrarEventoBlockchain
                //    de forma asíncrona una vez confirmada la transacción)
                $resolucion = Resolucion::create([
                    'codigo'           => $this->codigo,
                    'barrio_id'        => $this->barrio_id,
                    'titulo'           => $this->titulo,
                    'descripcion'      => $this->descripcion,
                    'service_type_id'    => $this->service_type_id,
                    'fecha_resolucion' => $this->fecha_resolucion,
                    'numero_firmas'    => $this->numero_firmas,
                    'numero_servicios' => $this->numero_servicios,
                    'evento_json'      => $this->evento_json ? json_decode($this->evento_json, true) : null,
                    'auth_status'      => $this->auth_status,
                ]);

                // 2. Almacenar el documento PDF y calcular HASH SHA-256
                $directory = 'resoluciones/' . date('Y/m');
                $filename = "{$resolucion->codigo}.pdf";

                $path = $this->documento_pdf->storeAs($directory, $filename, 'resoluciones');
                $fullPath = Storage::disk('resoluciones')->path($path);
                $hash = hash_file('sha256', $fullPath);
                $mime = $this->documento_pdf->getMimeType();

                // 3. Actualizar la resolución con la metadata del documento
                $resolucion->update([
                    'documento_original_path' => $path,
                    'documento_original_hash' => $hash,
                    'documento_original_mime' => $mime,
                ]);

                // 4. Registrar Eventos de Auditoría (dispatch a blockchain solo tras commit)
                $this->logResolucionEvents($resolucion, $userId, $path, $hash);

                return $resolucion;
            });

            session()->flash('message', 'Resolución creada, documentada y auditada correctamente.');

            return redirect()->route('resoluciones.lista');
        } catch (\Exception $e) {
            Log::error("Error registrando resolución: " . $e->getMessage());

            if ($path && Storage::disk('resoluciones')->exists($path)) {
                Storage::disk('resoluciones')->delete($path);
                Log::info("Archivo huérfano eliminado tras fallo de transacción: {$path}");
            }

            $this->addError('global', 'Error crítico al procesar la resolución. Intente nuevamente.');
        }
    }

    private function logResolucionEvents(Resolucion $resolucion, ?int $userId, string $path, string $hash): void
    {
        $auditCreacion = AuditEvent::logEvent(
            $resolucion,
            $userId,
            'resolucion_creada',
            [
                'codigo' => $resolucion->codigo,
                'service_type_id'   => $resolucion->service_type_id,
                'barrio' => $resolucion->barrio_id,
            ]
        );

        $auditDocumento = AuditEvent::logEvent(
            $resolucion,
            $userId,
            'resolucion_documento_subido',
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
        return view('livewire.operacion.resoluciones.create');
    }
}
