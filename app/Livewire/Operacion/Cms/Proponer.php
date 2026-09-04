<?php

namespace App\Livewire\Operacion\Cms;

use App\Models\{ContenidoSeccion, ContenidoItem, ContenidoVersion, ContenidoCampoDefinicion, AuditEvent};
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Facades\{Auth, DB, Gate, Storage, Log};
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\{Layout, Computed};

#[Layout('layouts.operacion')]
class Proponer extends Component
{
    use WithFileUploads;

    public ?int $contenidoSeccionId = null;
    public ?int $contenidoItemId = null;

    /** Valores de texto/url capturados dinámicamente */
    public array $valores = [];

    /** Archivos subidos dinámicamente */
    public array $archivosSubidos = [];

    public string $observaciones = '';

    public ?ContenidoSeccion $seccionExistente = null;
    public ?ContenidoItem $itemExistente = null;
    public array $imagenesExistentes = [];

    public function mount(?int $contenidoSeccionId = null, ?int $contenidoItemId = null)
    {
        Gate::authorize('cms.proponer');

        $this->contenidoSeccionId = $contenidoSeccionId;
        $this->contenidoItemId = $contenidoItemId;

        // dd($this->contenidoSeccionId, $this->contenidoItemId);

        if ($this->contenidoItemId) {
            // Cargar el ítem con su sección y las definiciones de campo ordenadas
            $this->itemExistente = ContenidoItem::with([
                'seccion.camposDefinicion' => fn($q) => $q->where('activo', true)->orderBy('orden'),
                'ultimaVersion'
            ])->findOrFail($this->contenidoItemId);

            $this->seccionExistente = $this->itemExistente->seccion;
            $this->contenidoSeccionId = $this->seccionExistente->id;

            // dd($this->itemExistente);

            if ($this->itemExistente->ultimaVersion) {
                $version = $this->itemExistente->ultimaVersion;
                $this->valores = $version->valores ?? [];

                if (!empty($version->archivos)) {
                    foreach ($version->archivos as $claveCampo => $datosArchivo) {
                        if (isset($datosArchivo['path'])) {
                            $this->imagenesExistentes[$claveCampo] = Storage::url($datosArchivo['path']);
                        }
                    }
                }
            }
        } elseif ($this->contenidoSeccionId) {
            $this->seccionExistente = ContenidoSeccion::with([
                'camposDefinicion' => fn($q) => $q->where('activo', true)->orderBy('orden')
            ])->findOrFail($this->contenidoSeccionId);
            // dd($this->seccionExistente);
        }
    }

    #[Computed]
    public function campos()
    {
        if (!$this->seccionExistente) {
            return collect();
        }

        // Si la relación ya fue cargada mediante mount(), usar los modelos en memoria
        if ($this->seccionExistente->relationLoaded('camposDefinicion')) {
            return $this->seccionExistente->camposDefinicion;
        }

        // Fallback: consulta directa a BD asegurando filtrar por la clave foránea correcta
        return ContenidoCampoDefinicion::where('contenido_seccion_id', $this->seccionExistente->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

    protected function rules(): array
    {
        $rules = ['observaciones' => 'nullable|string|max:500'];

        foreach ($this->campos as $campo) {
            $req = $campo->requerido ? 'required' : 'nullable';

            $rules["valores.{$campo->clave}"] = match ($campo->tipo_dato) {
                'texto'       => "{$req}|string|max:255",
                'texto_largo' => "{$req}|string",
                'url'         => [$req, 'string', 'max:500', function ($attr, $value, $fail) use ($campo) {
                    if (empty($value)) return;
                    if ($campo->url_externa_obligatoria && !preg_match('#^https?://#i', $value)) {
                        $fail("El campo \"{$campo->etiqueta}\" debe ser un enlace externo (http:// o https://).");
                    }
                }],
                default       => 'nullable',
            };

            if ($campo->tipo_dato === 'imagen') {
                $hasImage = !empty($this->imagenesExistentes[$campo->clave]);
                $imgReq = ($campo->requerido && !$hasImage) ? 'required' : 'nullable';
                $rules["archivosSubidos.{$campo->clave}"] = "{$imgReq}|image|max:10240";
            }

            if ($campo->tipo_dato === 'documento_pdf') {
                $hasPdf = !empty($this->imagenesExistentes[$campo->clave]);
                $pdfReq = ($campo->requerido && !$hasPdf) ? 'required' : 'nullable';
                $rules["archivosSubidos.{$campo->clave}"] = "{$pdfReq}|file|mimes:pdf|max:10240";
            }
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();
        $user = Auth::user();
        $rutasSubidas = [];

        try {
            DB::transaction(function () use ($user, &$rutasSubidas) {
                if (!$this->itemExistente) {
                    $ordenActual = ContenidoItem::where('contenido_seccion_id', $this->seccionExistente->id)->count() + 1;

                    $this->itemExistente = ContenidoItem::create([
                        'contenido_seccion_id' => $this->seccionExistente->id,
                        'identificador'        => $this->seccionExistente->area . '_slot_' . $ordenActual . '_' . Str::lower(Str::random(4)),
                        'orden'                => $ordenActual,
                        'activo'               => true,
                    ]);
                }

                $archivosData = [];
                if ($this->itemExistente->ultimaVersion && !empty($this->itemExistente->ultimaVersion->archivos)) {
                    $archivosData = $this->itemExistente->ultimaVersion->archivos;
                }

                foreach ($this->campos->whereIn('tipo_dato', ['imagen', 'documento_pdf']) as $campo) {
                    $file = $this->archivosSubidos[$campo->clave] ?? null;
                    if (!$file) continue;

                    if ($campo->tipo_dato === 'imagen') {
                        $image = Image::read($file->getRealPath());

                        if ($campo->imagen_ancho && $campo->imagen_alto) {
                            $image->cover($campo->imagen_ancho, $campo->imagen_alto, 'center');
                        }

                        $encoded = $image->encodeByExtension('webp', quality: 80);
                        $path = 'contenido/' . $this->seccionExistente->area . '/' . Str::uuid() . '.webp';

                        Storage::disk('public')->put($path, $encoded);
                        $mimeType = 'image/webp';
                    } else {
                        $path = $file->store('contenido/' . $this->seccionExistente->area, 'public');
                        $mimeType = $file->getMimeType();
                    }

                    $rutasSubidas[] = $path;
                    $archivosData[$campo->clave] = [
                        'path' => $path,
                        'hash' => hash_file('sha256', Storage::disk('public')->path($path)),
                        'mime' => $mimeType,
                    ];
                }

                $version = ContenidoVersion::create([
                    'contenido_item_id' => $this->itemExistente->id,
                    'numero_version'    => $this->itemExistente->siguienteNumeroVersion(),
                    'valores'           => $this->valores,
                    'archivos'          => $archivosData,
                    'auth_status'       => ContenidoVersion::ESTADO_PENDIENTE,
                    'propuesto_por'     => $user->id,
                    'fecha_propuesta'   => now(),
                    'observaciones'     => $this->observaciones,
                ]);

                $evento = AuditEvent::logEvent($version, $user->id, 'contenido_version_propuesta', [
                    'seccion' => $this->seccionExistente->area,
                    'item'    => $this->itemExistente->identificador,
                ]);

                DB::afterCommit(fn() => RegistrarEventoBlockchain::dispatch($evento->id));
            });
        } catch (\Throwable $e) {
            Log::error('[Cms\Proponer] Error al procesar propuesta', ['mensaje' => $e->getMessage()]);
            foreach ($rutasSubidas as $r) {
                if (Storage::disk('public')->exists($r)) Storage::disk('public')->delete($r);
            }
            $this->addError('global', 'Error al procesar la propuesta: ' . $e->getMessage());
            return;
        }

        session()->flash('message', 'Propuesta enviada correctamente.');
        return redirect()->route('cms.lista');
    }

    public function render()
    {
        return view('livewire.operacion.cms.proponer');
    }
}
