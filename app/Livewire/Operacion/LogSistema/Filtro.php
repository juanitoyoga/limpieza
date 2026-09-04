<?php

namespace App\Livewire\Operacion\LogSistema;

use App\Models\LogSistema;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Filtro extends Component
{
    public string $search = '';
    public string $nivel = '';
    public string $tipoOrigen = '';
    public string $fechaDesde = '';
    public string $fechaHasta = '';

    public function mount()
    {
        \Illuminate\Support\Facades\Gate::authorize('logs-sistema.ver');
    }

    public function updated($property)
    {
        if (in_array($property, ['search', 'nivel', 'tipoOrigen', 'fechaDesde', 'fechaHasta'])) {
            $this->dispatch('filtros-actualizados', filtros: [
                'search'     => $this->search,
                'nivel'      => $this->nivel,
                'tipoOrigen' => $this->tipoOrigen,
                'fechaDesde' => $this->fechaDesde,
                'fechaHasta' => $this->fechaHasta,
            ]);
        }
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'nivel', 'tipoOrigen', 'fechaDesde', 'fechaHasta']);
        $this->dispatch('filtros-actualizados', filtros: []);
    }

    public function render()
    {
        // tipos de origen distintos ya registrados, para poblar el select de chips
        $tiposOrigen = LogSistema::query()->distinct()->pluck('tipo_origen');

        return view('livewire.operacion.log-sistema.filtro', compact('tiposOrigen'));
    }
}
