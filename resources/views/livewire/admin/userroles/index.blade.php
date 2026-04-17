@section('page-title', 'Roles de Usuario')
@section('page-description', 'Mantenimiento de Roles Asignados')

<div class="overflow-x-auto">

    {{-- Barra de herramientas --}}
    <div class="flex flex-wrap items-center gap-3 mb-4">

        {{-- Buscador --}}
        <input type="text"
               wire:model.debounce.300ms="search"
               placeholder="Buscar rol..."
               class="border px-4 py-2 rounded min-w-[200px]">

        {{-- PerPage --}}
        <select wire:model.live="perPage"
                class="border px-4 py-2 rounded bg-white pr-10">
           
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>

        </select>


    </div>

    {{-- Tabla --}}
    <table class="w-full rounded shadow-sm overflow-hidden">
        <thead class="bg-gray-50">
            <tr>

                @foreach([
                    'id' => 'ID',
                    'user_id' => 'Usuario',
                    'role_id' => 'Rol',
                    'appointment_document' => 'Nombramiento',
                    'cessation_document' => 'Cesación',
                    'started_at' => 'Inicio',
                    'ended_at' => 'Fin',
                    'is_active' => 'Activo',
                ] as $field => $label)

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('{{ $field }}')">

                        <div class="flex items-center justify-between gap-2">
                            {{ $label }}

                            @if ($sortField === $field)
                                <span class="text-xs">
                                    @if ($sortDirection === 'asc') ▲ @else ▼ @endif
                                </span>
                            @endif
                        </div>

                    </th>

                @endforeach

                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                    Acciones
                </th>

            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">

            @foreach($userroles as $role)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 text-sm text-gray-900">{{ $role->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $role->user->first_name . ' ' . $role->user->last_name ?? 'Sin usuario' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $role->role->name ?? 'Sin rol' }}</td>

                    <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-xs">
                        {{ $role->appointment_document ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-900 truncate max-w-xs">
                        {{ $role->cessation_document ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-900">{{ $role->started_at?->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $role->ended_at?->format('Y-m-d') ?? '-' }}</td>

                    <td class="px-6 py-4 text-sm">
                        @if($role->is_active)
                            <span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-red-200 text-red-800 rounded">Inactivo</span>
                        @endif
                    </td>

                    {{-- Acciones --}}
                    <td class="px-6 py-4 text-sm">

                        <div class="flex gap-4">

                            <a href="{{ route('userroles.show', $role->id) }}"
                               class="text-green-600 hover:underline">
                                Ver
                            </a>

                        </div>

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $userroles->links() }}
    </div>

</div>
