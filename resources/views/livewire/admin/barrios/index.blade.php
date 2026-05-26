@section('page-title', 'Barrios')
@section('page-description', 'Mantenimiento de Registros')

<div x-data="{ scroll: false }">

    {{-- Mensajes de sesión --}}
    @if(session()->has('message'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
        <i class="fas fa-check-circle text-green-500 mr-3"></i>
        <p class="text-green-800 font-medium">{{ session('message') }}</p>
    </div>
    @endif

    {{-- Barra de herramientas --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <input type="text"
            wire:model.debounce.300ms="search"
            placeholder="Buscar barrio, sector, parroquia..."
            class="border px-4 py-2 rounded flex-1 min-w-[200px]">

        <select wire:model.live="perPage"
            class="border px-4 py-2 rounded bg-white">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>

        <a href="{{ route('barrios.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Crear Barrio
        </a>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    @foreach([
                    'id' => 'ID',
                    'id_DMQ' => 'Código GeoPis',
                    'nombre' => 'Nombre',
                    'sector' => 'Sector',
                    'parroquia'=> 'Parroquia',
                    ] as $field => $label)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('{{ $field }}')">
                        <div class="flex items-center gap-1">
                            {{ $label }}
                            @if($sortField === $field)
                            <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </div>
                    </th>
                    @endforeach
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mapa</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($barrios as $barrio)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $barrio->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $barrio->id_DMQ }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $barrio->nombre }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $barrio->sector }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $barrio->parroquia }}</td>

                    {{-- Indicadores de mapa --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Centroide --}}
                            @if(!empty($barrio->coordenadas))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700"
                                title="Lat: {{ $barrio->coordenadas['lat'] }}, Lng: {{ $barrio->coordenadas['lng'] }}">
                                <i class="fas fa-map-marker-alt mr-1"></i> Centroide
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-400">
                                <i class="fas fa-map-marker-alt mr-1"></i> Sin centro
                            </span>
                            @endif

                            {{-- Polígono --}}
                            @if(!empty($barrio->polygon))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-700"
                                title="{{ count($barrio->polygon) }} puntos">
                                <i class="fas fa-draw-polygon mr-1"></i> {{ count($barrio->polygon) }}pts
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-400">
                                <i class="fas fa-draw-polygon mr-1"></i> Sin polígono
                            </span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm">
                        <div class="flex gap-3 justify-center">
                            <a href="{{ route('barrios.edit', $barrio->id) }}"
                                class="text-blue-600 hover:underline">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <button wire:click="confirmDelete({{ $barrio->id }})"
                                class="text-red-600 hover:underline">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                        <i class="fas fa-city text-3xl mb-2 block"></i>
                        No se encontraron barrios.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $barrios->links() }}
    </div>

    {{-- Modal confirmar eliminación --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl max-w-sm w-full mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-bold text-gray-900">¿Eliminar este barrio?</h3>
            </div>
            <p class="text-gray-600 mb-6 text-sm">
                Esta acción no se puede deshacer. El barrio y sus datos geográficos serán eliminados.
            </p>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('confirmingDelete', false)"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button wire:click="delete"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i>Sí, eliminar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>