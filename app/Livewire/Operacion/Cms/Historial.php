<?php

namespace App\Livewire\Operacion\Cms;

use App\Models\ContenidoItem;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Attributes\{Layout, Computed};

#[Layout('layouts.operacion')]
class Historial extends Component
{
    public ContenidoItem $item;

    public function mount(ContenidoItem $item): void
    {
        Gate::authorize('cms.ver');

        $this->item = $item->load('seccion', 'versionPublicada');
    }

    /**
     * Todas las versiones de este item, más recientes primero, con
     * quién propuso/aprobó/rechazó cada una para el timeline.
     */
    #[Computed]
    public function versiones()
    {
        return $this->item->versiones()
            ->with(['proponente', 'aprobador', 'rechazador'])
            ->orderByDesc('numero_version')
            ->get();
    }

    #[Computed]
    public function campos()
    {
        $seccion = $this->item->seccion;

        if ($seccion->relationLoaded('camposDefinicion')) {
            return $seccion->camposDefinicion;
        }

        return \App\Models\ContenidoCampoDefinicion::where('contenido_seccion_id', $seccion->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

    public function render()
    {
        return view('livewire.operacion.cms.historial');
    }
}
