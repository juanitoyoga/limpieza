@section('page-title', 'Barrios')
    
@section('page-description', 'Mantenimiento de Registros')

    <!-- Tabla de registros -->
    <div class="overflow-x-auto">

        <div class="gap-3">

            <input type="text"
                   wire:model.debounce.300ms="search"
                   placeholder="Buscar barrio..."
                   class="border px-4 py-2 rounded flex-1 min-w-[200px]">
        
            <select wire:model.live="perPage"
                   class="border px-4 py-2 rounded appearance-none pr-10 bg-white">
           
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        
            <button @click="scroll = !scroll"
                    class="bg-gray-600 text-white px-4 py-2 rounded">
                Scroll
            </button>
        
            <a href="{{ route('barrios.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                + Crear
            </a>
        </div>
      

        <table class="w-full">
            <thead class="bg-gray-50">

                <tr>

                    {{-- Encabezados con sort --}}
                    @foreach([
                        'id' => 'ID',
                        'id_DMQ' => 'Código GeoPis',
                        'nombre' => 'Nombre',
                        'sector' => 'Sector',
                        'parroquia' => 'Parroquia',
                    ] as $field => $label)

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                    wire:click="sortBy('{{ $field }}')">

                                <div class="flex items-center justify-between">
                                    {{ $label }}

                                    @if ($sortField === $field)
                                        <span>
                                            @if ($sortDirection === 'asc')
                                                ▲
                                            @else
                                                ▼
                                            @endif
                                        </span>
                                    @endif
                                </div>

                    </th>
                    @endforeach
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($barrios as $barrio)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $barrio->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $barrio->id_DMQ }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $barrio->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $barrio->sector }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $barrio->parroquia }}</td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex gap-3 justify-center">
                                    <a href="{{ route('barrios.show', $barrio->id) }}"
                                       class="text-green-600 hover:underline">Ver</a>

                                    <a href="{{ route('barrios.edit', $barrio->id) }}"
                                       class="text-blue-600 hover:underline">Editar</a>

                                    <button wire:click="confirmDelete({{ $barrio->id }})"
                                            class="text-red-600 hover:underline">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Modal eliminar -->
            @if($confirmingDelete)
                <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded shadow-xl">
                        <h3 class="text-lg font-bold mb-4">¿Eliminar este barrio?</h3>

                        <div class="flex gap-4">
                            <button wire:click="delete"
                                    class="bg-red-600 text-white px-4 py-2 rounded">
                                Sí, eliminar
                            </button>

                            <button wire:click="$set('confirmingDelete', false)"
                                    class="bg-gray-300 px-4 py-2 rounded">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        <!-- PAGINACIÓN -->
        <div class="mt-4">
            {{ $barrios->links() }}
        </div>
            
    </div>
