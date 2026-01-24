
@section('page-title', 'Contravenciones')
    
@section('page-description', 'Mantenimiento de Registros')

    <!-- Tabla de registros -->
    <div class="overflow-x-auto">

        <div class="gap-3">

            <input type="text"
                   wire:model.debounce.300ms="search"
                   placeholder="Buscar contravencion..."
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
        
            <a href="{{ route('ordenanzas.create') }}"
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
                            'codigo' => 'codigo',
                            'tipo' => 'tipo',
                            'descripcion' => 'descripcion',
                            'nivel_gravedad' => 'nivel_gravedad',
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
                    @foreach($ordenanzas as $ordenanza)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ordenanza->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ordenanza->codigo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ordenanza->tipo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ordenanza->descripcion }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ordenanza->nivel_gravedad }}</td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex gap-3 justify-center">
                                    <a href="{{ route('ordenanzas.show', $ordenanza->id) }}"
                                       class="text-green-600 hover:underline">Ver</a>

                                    <a href="{{ route('ordenanzas.edit', $ordenanza->id) }}"
                                       class="text-blue-600 hover:underline">Editar</a>

                                    <button wire:click="confirmDelete({{ $ordenanza->id }})"
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
                        <h3 class="text-lg font-bold mb-4">¿Eliminar esta ordenanza?</h3>

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
            {{ $ordenanzas->links() }}
        </div>
            
    </div>
