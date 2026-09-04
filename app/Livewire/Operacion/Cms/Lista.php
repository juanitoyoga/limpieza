<?php

namespace App\Livewire\Operacion\Cms;

use App\Models\{ContenidoSeccion, ContenidoItem, ContenidoVersion};
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Lista extends Component
{
    /** Área activa en las pestañas */
    public string $areaActiva = 'banner';

    public string $filtroEstado = '';

    public function mount(): void
    {
        Gate::authorize('cms.ver');
    }

    public function seleccionarArea(string $area): void
    {
        $this->areaActiva = $area;
        $this->filtroEstado = '';
    }

    /**
     * Pestañas derivadas dinámicamente de las secciones configuradas en BD
     */
    public function areas(): array
    {
        return ContenidoSeccion::areasDisponibles()->toArray();
    }

    /** Sección actualmente seleccionada según el área en pantalla */
    public function seccionActiva(): ?ContenidoSeccion
    {
        return ContenidoSeccion::activaPara($this->areaActiva);
    }

    public function esColeccion(): bool
    {
        $seccion = $this->seccionActiva();
        return $seccion && $seccion->multiplicidad !== 'unico';
    }

    /** Oculta el botón de creación si una colección limitada alcanzó su tope de slots */
    public function alcanzoMaximo(): bool
    {
        $seccion = $this->seccionActiva();

        if (!$seccion || $seccion->multiplicidad !== 'coleccion_limitada') {
            return false;
        }

        return ContenidoItem::where('contenido_seccion_id', $seccion->id)
            ->where('activo', true)
            ->count() >= $seccion->max_items;
    }

    public function render()
    {
        $seccion = $this->seccionActiva();

        $items = $seccion
            ? ContenidoItem::query()
            ->where('contenido_seccion_id', $seccion->id)
            ->where('activo', true)
            ->with([
                'versionPublicada',
                'ultimaVersion',
                'versiones' => fn($q) => $q->when(
                    $this->filtroEstado,
                    fn($qq) => $qq->where('auth_status', $this->filtroEstado)
                ),
            ])
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            : collect();

        return view('livewire.operacion.cms.lista', [
            'items'   => $items,
            'seccion' => $seccion,
            'areas'   => $this->areas(),
            'estados' => [
                ContenidoVersion::ESTADO_PENDIENTE,
                ContenidoVersion::ESTADO_APROBADA,
                ContenidoVersion::ESTADO_RECHAZADA,
                ContenidoVersion::ESTADO_PUBLICADA,
                ContenidoVersion::ESTADO_ARCHIVADA,
            ],
        ]);
    }
}
