<div x-data="{ scroll: true }" class="p-4 sm:p-6 bg-white shadow rounded">

    <h2 class="text-xl font-bold mb-4">Listado de Contravenciones</h2>

    <!-- FILTROS -->
    <div class="flex flex-wrap items-center gap-3 mb-4">

        <input type="text"
               wire:model.debounce.300ms="search"
               placeholder="Buscar contravencion..."
               class="border px-4 py-2 rounded flex-1 min-w-[200px]">
    
        <select wire:model.live="perPage"
                class="border px-3 py-2 rounded">
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
    

    <!-- Contador de resultados -->
    <p class="text-sm text-gray-600 mb-2">
        Resultados: <strong>{{ $ordenanzas->total() }}</strong>
    </p>

    <!-- TABLA -->
    <div :class="scroll ? 'overflow-y-auto max-h-96 border rounded' : 'border rounded'">
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-emerald-500 text-gray-900 sticky top-0">

                    <tr>

                        {{-- Encabezados con sort --}}
                        @foreach([
                            'id' => 'ID',
                            'codigo' => 'codigo',
                            'tipo' => 'tipo',
                            'descripcion' => 'descripcion',
                            'nivel_gravedad' => 'nivel_gravedad',
                        ] as $field => $label)

                            <th class="p-2 border cursor-pointer select-none"
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

                        <th class="p-2 border">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($ordenanzas as $ordenanza)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 border">{{ $ordenanza->id }}</td>
                            <td class="p-2 border">{{ $ordenanza->codigo }}</td>
                            <td class="p-2 border">{{ $ordenanza->descripcion }}</td>
                            <td class="p-2 border">{{ $ordenanza->tipo }}</td>
                            <td class="p-2 border">{{ $ordenanza->nivel_gravedad }}</td>

                            <td class="p-2 border">
                                <div class="flex gap-3 justify-center">
                                    <a href="{{ route('ordenanzas.show', $ordenanza) }}"
                                       class="text-green-600 hover:underline">Ver</a>

                                    <a href="{{ route('ordenanzas.edit', $ordenanza) }}"
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
                        <h3 class="text-lg font-bold mb-4">¿Eliminar esta contravencion?</h3>

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

        </div>
    </div>

    <!-- PAGINACIÓN -->
    <div class="mt-4">
        {{ $ordenanzas->links() }}
    </div>
</div>
