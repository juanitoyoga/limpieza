@section('page-title', 'Lista de Vecinos')
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
            <a href="{{ route('vecinos.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm flex items-center gap-1">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                </svg>
                Filtros
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
    @if($cedula || $nombre || $email || $id_DMQ || $barrio || $parroquia || $activo !== '')
    <div class="flex flex-wrap gap-2 mb-4">
        <span class="text-xs text-gray-500 py-1">Filtros activos:</span>

        @if($cedula)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Cédula: {{ $cedula }}
        </span>
        @endif

        @if($nombre)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Nombre: {{ $nombre }}
        </span>
        @endif

        @if($email)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Email: {{ $email }}
        </span>
        @endif

        @if($id_DMQ)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            GeoPis: {{ $id_DMQ }}
        </span>
        @endif

        @if($barrio)
        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
            Barrio: {{ $barrio }}
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
                    @foreach([
                    'last_name' => 'Usuario',
                    'email' => 'Email',
                    'cedula' => 'Cédula',
                    'direccion' => 'Dirección',
                    'barrio' => 'Barrio',
                    'parroquia' => 'Parroquia'
                    ] as $field => $label)
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
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($usuarios as $user)
                <tr class="hover:bg-gray-50 transition {{ !$user->vecino->is_active ? 'opacity-60' : '' }}">

                    {{-- Usuario --}}
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $user->full_name }}
                    </td>

                    {{-- Email --}}
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->email }}
                    </td>

                    {{-- Cédula --}}
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->vecino->cedula }}
                    </td>

                    {{-- Dirección --}}
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->vecino->calle_principal }}
                        {{ $user->vecino->numero }},
                        {{ $user->vecino->calle_secundaria }}
                    </td>

                    {{-- Barrio --}}
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->vecino->barrio->nombre ?? '—' }}
                    </td>

                    {{-- Parroquia --}}
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $user->vecino->barrio->parroquia ?? '—' }}
                    </td>

                    {{-- Estado --}}
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            {{ $user->vecino->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->vecino->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $user->vecino->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-6 py-4">
                        <div class="flex gap-2 justify-center">

                            {{-- Ver --}}
                            <a href="{{ route('vecinos.show', $user->id) }}"
                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition inline-flex items-center"
                                title="Ver detalle">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </a>



                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        No se encontraron vecinos con los filtros aplicados.
                        <br>
                        <a href="{{ route('vecinos.index') }}" class="text-blue-500 hover:underline text-sm mt-2 inline-block">
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
        {{ $usuarios->links() }}
    </div>
</div>