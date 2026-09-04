<?php

namespace App\Livewire\Operacion\Cms;

use App\Models\ContenidoSeccion;
use App\Models\ContenidoCampoDefinicion;
use App\Models\ContenidoItem;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class GestionSecciones extends Component
{
    public ?int $seccionSeleccionadaId = null;
    public string $pestanaActiva = 'campos'; // 'campos' o 'items'

    // Formulario Sección
    public bool $mostrarModalSeccion = false;
    public string $area = '';
    public string $version_spec = 'v1.0';
    public string $multiplicidad = 'uno';
    public ?int $max_items = null;
    public string $plataforma = 'web';
    public ?string $descripcion = '';

    // Formulario Campo Definición
    public bool $mostrarModalCampo = false;
    public ?int $campoEditingId = null;
    public string $clave = '';
    public string $etiqueta = '';
    public string $tipo_dato = ContenidoCampoDefinicion::TIPO_DATO_TEXTO;
    public bool $requerido = false;
    public bool $url_externa_obligatoria = false;
    public ?int $imagen_ancho = null;
    public ?int $imagen_alto = null;
    public int $orden = 1;

    public function selectSeccion(int $id)
    {
        $this->seccionSeleccionadaId = $id;
        $this->resetCampoForm();
    }

    // --- GESTIÓN DE ÍTEMS / SLOTS DE CONTENIDO ---
    public function crearItemSlot()
    {
        $seccion = ContenidoSeccion::withCount('items')->findOrFail($this->seccionSeleccionadaId);

        if ($seccion->multiplicidad === 'uno' && $seccion->items_count >= 1) {
            session()->flash('item_error', 'Esta sección está configurada como Ítem Único.');
            return;
        }

        if ($seccion->max_items && $seccion->items_count >= $seccion->max_items) {
            session()->flash('item_error', "Límite máximo de ítems alcanzado ({$seccion->max_items}).");
            return;
        }

        $nuevoOrden = $seccion->items_count + 1;
        $identificador = $seccion->area . '_slot_' . $nuevoOrden . '_' . Str::lower(Str::random(4));

        ContenidoItem::create([
            'contenido_seccion_id' => $seccion->id,
            'identificador'        => $identificador,
            'orden'                => $nuevoOrden,
            'activo'               => true,
        ]);

        session()->flash('item_msg', 'Slot de ítem generado.');
    }

    public function deleteItemSlot(int $itemId)
    {
        ContenidoItem::where('id', $itemId)
            ->where('contenido_seccion_id', $this->seccionSeleccionadaId)
            ->delete();

        session()->flash('item_msg', 'Slot eliminado correctamente.');
    }

    // --- MÉTODOS DE SECCIÓN Y CAMPOS ---
    public function openModalSeccion()
    {
        $this->reset(['area', 'version_spec', 'multiplicidad', 'max_items', 'plataforma', 'descripcion']);
        $this->mostrarModalSeccion = true;
    }

    public function saveSeccion()
    {
        $this->validate([
            'area'          => 'required|string|max:100',
            'version_spec'  => 'required|string|max:20',
            'multiplicidad' => 'required|in:uno,varios',
            'max_items'     => 'nullable|integer|min:1',
            'plataforma'    => 'required|string|max:50',
            'descripcion'   => 'nullable|string|max:255',
        ]);

        $seccion = ContenidoSeccion::create([
            'area'          => strtolower(trim($this->area)),
            'version_spec'  => $this->version_spec,
            'multiplicidad' => $this->multiplicidad,
            'max_items'     => $this->multiplicidad === 'uno' ? 1 : $this->max_items,
            'plataforma'    => $this->plataforma,
            'descripcion'   => $this->descripcion,
            'activo'        => true,
        ]);

        $this->seccionSeleccionadaId = $seccion->id;
        $this->mostrarModalSeccion = false;
        session()->flash('seccion_msg', 'Estructura de sección registrada.');
    }

    public function openModalCampo(?int $campoId = null)
    {
        if (!$this->seccionSeleccionadaId) return;
        $this->resetCampoForm();

        if ($campoId) {
            $campo = ContenidoCampoDefinicion::findOrFail($campoId);
            $this->campoEditingId          = $campo->id;
            $this->clave                   = $campo->clave;
            $this->etiqueta                = $campo->etiqueta;
            $this->tipo_dato               = $campo->tipo_dato;
            $this->requerido               = $campo->requerido;
            $this->url_externa_obligatoria = $campo->url_externa_obligatoria;
            $this->imagen_ancho            = $campo->imagen_ancho;
            $this->imagen_alto             = $campo->imagen_alto;
            $this->orden                   = $campo->orden;
        } else {
            $maxOrden = ContenidoCampoDefinicion::where('contenido_seccion_id', $this->seccionSeleccionadaId)->max('orden');
            $this->orden = ($maxOrden ?? 0) + 1;
        }

        $this->mostrarModalCampo = true;
    }

    public function saveCampo()
    {
        if (!$this->seccionSeleccionadaId) return;

        $this->validate([
            'clave' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('contenido_campo_definiciones', 'clave')
                    ->where('contenido_seccion_id', $this->seccionSeleccionadaId)
                    ->ignore($this->campoEditingId),
            ],
            'etiqueta'  => 'required|string|max:100',
            'tipo_dato' => 'required|string|in:texto,texto_largo,url,imagen,documento_pdf',
            'orden'     => 'required|integer|min:1',
        ]);

        ContenidoCampoDefinicion::updateOrCreate(
            ['id' => $this->campoEditingId],
            [
                'contenido_seccion_id'    => $this->seccionSeleccionadaId,
                'clave'                   => $this->clave,
                'etiqueta'                => $this->etiqueta,
                'tipo_dato'               => $this->tipo_dato,
                'requerido'               => $this->requerido,
                'url_externa_obligatoria' => $this->tipo_dato === 'url' ? $this->url_externa_obligatoria : false,
                'imagen_ancho'            => $this->tipo_dato === 'imagen' ? $this->imagen_ancho : null,
                'imagen_alto'             => $this->tipo_dato === 'imagen' ? $this->imagen_alto : null,
                'orden'                   => $this->orden,
                'activo'                  => true,
            ]
        );

        $this->mostrarModalCampo = false;
        $this->resetCampoForm();
        session()->flash('campo_msg', 'Campo configurado correctamente.');
    }

    public function deleteCampo(int $campoId)
    {
        ContenidoCampoDefinicion::where('id', $campoId)
            ->where('contenido_seccion_id', $this->seccionSeleccionadaId)
            ->delete();

        session()->flash('campo_msg', 'Campo eliminado.');
    }

    private function resetCampoForm()
    {
        $this->reset(['campoEditingId', 'clave', 'etiqueta', 'tipo_dato', 'requerido', 'url_externa_obligatoria', 'imagen_ancho', 'imagen_alto', 'orden']);
        $this->tipo_dato = ContenidoCampoDefinicion::TIPO_DATO_TEXTO;
    }

    public function render()
    {
        $secciones = ContenidoSeccion::withCount('items')->orderBy('area')->get();

        $seccionSeleccionada = $this->seccionSeleccionadaId
            ? ContenidoSeccion::with([
                'camposDefinicion',
                'items.versionPublicada',
                'items.ultimaVersion'
            ])->find($this->seccionSeleccionadaId)
            : null;

        return view('livewire.operacion.cms.gestion-secciones', [
            'secciones' => $secciones,
            'seccionSeleccionada' => $seccionSeleccionada,
        ]);
    }
}
