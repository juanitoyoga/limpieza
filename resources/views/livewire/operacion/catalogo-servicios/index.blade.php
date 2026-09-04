@section('page-title', 'Catálogo de Servicios')
@section('page-description', 'Catálogo de servicios utilizados en resoluciones y ofertas')

<div class="p-6">

    @if (session('message'))
    <div class="bg-green-50 text-green-700 border border-green-200 rounded px-3 py-2 mb-4 text-sm">
        {{ session('message') }}
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-50 text-red-700 border border-red-200 rounded px-3 py-2 mb-4 text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">

        <div class="flex flex-wrap items-end gap-3">
            <div class="flex flex-col">
                <label class="text-sm text-gray-700 mb-1">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Código, nombre, tipo, subtipo o ámbito..."
                    class="border rounded px-3 py-2 w-64">
            </div>

            <div class="flex flex-col">
                <label class="text-sm text-gray-700 mb-1">Tipo</label>
                <select wire:model.live="filterTipo" class="border rounded px-3 py-2 w-48">
                    <option value="">Todos</option>
                    @foreach ($tiposDisponibles as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm text-gray-700 mb-1">Estado</label>
                <select wire:model.live="filterEstado" class="border rounded px-3 py-2 w-40">
                    <option value="">Todos</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm text-gray-700 mb-1">Filas por página</label>
                <select wire:model.live="perPage" class="border rounded px-3 py-2 w-28">
                    @foreach ($opcionesPerPage as $opcion)
                    <option value="{{ $opcion }}">{{ $opcion }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-sm text-gray-700 mb-1">&nbsp;</label>
                <a href="{{ route('catalogo-servicios.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded whitespace-nowrap">
                    Nuevo Servicio
                </a>
            </div>
        </div>

    </div>

    <table class="w-full border text-sm bg-white">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 text-left cursor-pointer" wire:click="sortBy('codigo')">
                    Código
                    @if ($sortField === 'codigo') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                </th>
                <th class="p-2 text-left cursor-pointer" wire:click="sortBy('nombre')">
                    Nombre
                    @if ($sortField === 'nombre') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                </th>
                <th class="p-2 text-left cursor-pointer" wire:click="sortBy('service_type_id')">
                    Tipo
                    @if ($sortField === 'service_type_id') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                </th>
                <th class="p-2 text-left">Subtipo</th>
                <th class="p-2 text-left">Ámbito</th>
                <th class="p-2 text-left">Frecuencia</th>
                <th class="p-2 text-left">Costo ref.</th>
                <th class="p-2 text-left">Estado</th>
                <th class="p-2 text-right">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($items as $item)
            <tr class="border-t">
                <td class="p-2">{{ $item->codigo }}</td>
                <td class="p-2">{{ $item->nombre }}</td>
                <td class="p-2">{{ $item->serviceType?->name ?? '—' }}</td>
                <td class="p-2">{{ $item->serviceSubtype?->name ?? '—' }}</td>
                <td class="p-2">{{ $item->serviceScope?->name ?? '—' }}</td>
                <td class="p-2">{{ $item->frequency?->name ?? '—' }}</td>
                <td class="p-2">
                    {{ $item->costo_referencial !== null ? number_format($item->costo_referencial, 2) : '—' }}
                </td>
                <td class="p-2">
                    <span class="px-2 py-1 rounded text-xs {{ $item->estado ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $item->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td class="p-2 text-right space-x-2">
                    <a href="{{ route('catalogo-servicios.edit', $item->id) }}"
                        title="Editar"
                        class="text-blue-500 hover:text-blue-800 transition">
                        <i class="fas fa-pen"></i>
                    </a>
                    <button wire:click="confirmDelete({{ $item->id }})"
                        title="Eliminar"
                        class="text-red-500 hover:text-red-800 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty

            <tr>
                <td colspan="9" class="p-6 text-center text-gray-500">No hay servicios registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $items->links() }}
    </div>

    @if ($confirmingDelete)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm">
            <h3 class="font-semibold text-lg mb-2">¿Eliminar servicio?</h3>
            <p class="text-sm text-gray-600 mb-4">
                Esta acción no se puede deshacer. Si el servicio ya está referenciado en una resolución
                u oferta, no podrá eliminarse.
            </p>
            <div class="flex justify-end gap-3">
                <button wire:click="cancelDelete" class="px-4 py-2 rounded border">Cancelar</button>
                <button wire:click="delete" class="px-4 py-2 rounded bg-red-600 text-white">Eliminar</button>
            </div>
        </div>
    </div>
    @endif
</div>