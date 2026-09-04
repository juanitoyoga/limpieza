{{-- livewire/operacion/log-sistema/filtro.blade.php --}}
<div class="bg-white p-4 rounded-xl shadow border border-gray-200 mb-4 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
        <input type="text" wire:model.live.debounce.300ms="search"
            placeholder="Comentario, origen, mensaje..." class="border px-3 py-2 rounded w-64">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Nivel</label>
        <select wire:model.live="nivel" class="border px-3 py-2 rounded">
            <option value="">Todos</option>
            <option value="info">Info</option>
            <option value="warning">Warning</option>
            <option value="error">Error</option>
            <option value="critical">Critical</option>
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Tipo de origen</label>
        <select wire:model.live="tipoOrigen" class="border px-3 py-2 rounded">
            <option value="">Todos</option>
            @foreach($tiposOrigen as $tipo)
            <option value="{{ $tipo }}">{{ $tipo }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
        <input type="date" wire:model.live="fechaDesde" class="border px-3 py-2 rounded">
    </div>

    <div>
        <label class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
        <input type="date" wire:model.live="fechaHasta" class="border px-3 py-2 rounded">
    </div>

    <button wire:click="limpiarFiltros" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-600">
        <i class="fas fa-rotate-left mr-1"></i> Limpiar
    </button>
</div>