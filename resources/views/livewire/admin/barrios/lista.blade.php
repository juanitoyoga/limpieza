@section('page-title', 'Lista de Barrios')
@section('page-description', 'Resultados de búsqueda')

<div>
    {{-- Mensajes --}}
    @if(session()->has('message'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
        <svg class="w-5 h-5 text-green-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <p class="text-green-800 font-medium">{{ session('message') }}</p>
    </div>
    @endif

    {{-- Barra superior --}}
    <div class="flex flex-wrap gap-3 mb-4 items-center justify-between">
        <div class="flex gap-2">
            <a href="{{ route('barrios.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm flex items-center gap-1">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                </svg>
                Filtros
            </a>
            <a href="{{ route('barrios.create') }}"
                class="px-4 py-2 bg-white border hover:bg-blue-400 border-green-600 text-green-700  hover:text-white rounded-lg shadow-sm transition-all duration-200 text-sm flex items-center gap-1.5 font-medium">
                <svg class="w-4 h-4 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Barrio
            </a>
        </div>
        <select wire:model.live="perPage"
            class="border px-3 py-2 rounded-lg bg-white text-sm">
            <option value="5">5 por página</option>
            <option value="10">10 por página</option>
            <option value="25">25 por página</option>
            <option value="50">50 por página</option>
        </select>
    </div>

    {{-- Filtros activos --}}
    @if($id_DMQ || $nombre || $sector || $parroquia || $activo !== '')
    <div class="flex flex-wrap gap-2 mb-4">
        <span class="text-xs text-gray-500 py-1">Filtros activos:</span>
        @if($id_DMQ)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            GeoPis: {{ $id_DMQ }}
        </span>
        @endif
        @if($nombre)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Nombre: {{ $nombre }}
        </span>
        @endif
        @if($sector)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Sector: {{ $sector }}
        </span>
        @endif
        @if($parroquia)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Parroquia: {{ $parroquia }}
        </span>
        @endif
        @if($activo !== '')
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Estado: {{ $activo === '1' ? 'Activos' : 'Inactivos' }}
        </span>
        @endif
    </div>
    @endif

    {{-- Tabla --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    @foreach(['id' => 'ID', 'id_DMQ' => 'GeoPis', 'nombre' => 'Nombre', 'sector' => 'Sector', 'parroquia' => 'Parroquia'] as $field => $label)
                    <th wire:click="sortBy('{{ $field }}')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100 select-none">
                        <div class="flex items-center gap-1">
                            {{ $label }}
                            @if($sortField === $field)
                            <span>{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </div>
                    </th>
                    @endforeach
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mapa</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($barrios as $barrio)
                <tr class="hover:bg-gray-50 transition {{ !$barrio->activo ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $barrio->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $barrio->id_DMQ }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $barrio->nombre }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $barrio->sector }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $barrio->parroquia }}</td>

                    {{-- Estado activo --}}
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            {{ $barrio->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $barrio->activo ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $barrio->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>

                    {{-- Mapa --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if(!empty($barrio->coordenadas))
                            <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700 flex items-center gap-1"
                                title="Lat: {{ $barrio->coordenadas['lat'] }}, Lng: {{ $barrio->coordenadas['lng'] }}">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                C
                            </span>
                            @else
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                —
                            </span>
                            @endif

                            @if(!empty($barrio->polygon))
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 flex items-center gap-1"
                                title="{{ count($barrio->polygon) }} puntos">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m11.142 0L21.75 12l-4.179-2.25M12 5.75 7.821 8 12 10.25 16.179 8 12 5.75ZM12 18.25l-4.179-2.25L12 18.25l4.179-2.25L12 18.25Z" />
                                </svg>
                                {{ count($barrio->polygon) }}
                            </span>
                            @else
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m11.142 0L21.75 12l-4.179-2.25M12 5.75 7.821 8 12 10.25 16.179 8 12 5.75ZM12 18.25l-4.179-2.25L12 18.25l4.179-2.25L12 18.25Z" />
                                </svg>
                                —
                            </span>
                            @endif
                        </div>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-center">
                            {{-- Ver detalle (Eye) --}}
                            <a href="{{ route('barrios.show', $barrio->id) }}"
                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition inline-flex items-center"
                                title="Ver detalle">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>

                            {{-- Editar (Pencil) --}}
                            <a href="{{ route('barrios.edit', $barrio->id) }}"
                                class="p-1.5 text-gray-500 hover:text-yellow-600 hover:bg-yellow-50 rounded transition inline-flex items-center"
                                title="Editar">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </a>

                            {{-- Toggle Estado (Toggle On/Off simulado con Heroicons) --}}
                            <button wire:click="toggle({{ $barrio->id }})"
                                class="p-1.5 rounded transition inline-flex items-center
                                    {{ $barrio->activo
                                        ? 'text-gray-500 hover:text-orange-600 hover:bg-orange-50'
                                        : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}"
                                title="{{ $barrio->activo ? 'Desactivar' : 'Activar' }}">
                                @if($barrio->activo)
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                @else
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                @endif
                            </button>

                            {{-- Eliminar (Trash) --}}
                            <button wire:click="confirmDelete({{ $barrio->id }})"
                                class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition inline-flex items-center"
                                title="Eliminar">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        No se encontraron barrios con los filtros aplicados.
                        <br>
                        <a href="{{ route('barrios.index') }}" class="text-blue-500 hover:underline text-sm mt-2 inline-block">
                            Volver a los filtros
                        </a>
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

    {{-- Modal eliminación --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl max-w-sm w-full mx-4">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 text-red-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <h3 class="text-lg font-bold text-gray-900">¿Eliminar este barrio?</h3>
            </div>
            <p class="text-gray-600 mb-6 text-sm">
                Esta acción no se puede deshacer. El barrio y sus datos geográficos serán eliminados permanentemente.
            </p>
            <div class="flex gap-3 justify-end">
                <button wire:click="$set('confirmingDelete', false)"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button wire:click="delete"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>