<div x-data="{ scroll: true }" class="p-4 sm:p-6 bg-white shadow rounded">

    <h2 class="text-xl font-bold mb-4">Listado de Porcentaje de Multas</h2>

    <!-- FILTROS -->
    <div class="flex flex-wrap items-center gap-3 mb-4">

        <input type="text"
               wire:model.debounce.300ms="search"
               placeholder="Buscar multas..."
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
    
        <a href="{{ route('porcentajes.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded">
            + Crear
        </a>
    </div>
    {{-- id 	
    ordenanza332_id 	
    salariominimo_id 	
    porcentaje 		
    codigo 	
    tipo
    descripcion
    nivel_gravedad
    year 	
    valor_usd 	   --}}

    <!-- Contador de resultados -->
    <p class="text-sm text-gray-600 mb-2">
        Resultados: <strong>{{ $multas->total() }}</strong>
    </p>

    <!-- TABLA -->
    <div :class="scroll ? 'overflow-y-auto max-h-96 border rounded' : 'border rounded'">
        <div class="overflow-x-auto">

            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-emerald-500 text-gray-900 sticky top-0">

                    <tr>
                        <th class="px-4 py-2">Código</th>
                        <th class="px-4 py-2">Descripción</th>
                        <th class="px-4 py-2">Año</th>
                        <th class="px-4 py-2">Salario</th>
                        <th class="px-4 py-2">Porcentaje (%)</th>
                        <th class="px-4 py-2">Multa (USD)</th>
                        <th class="px-4 py-2 text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($multas as $item)

                    <tr class="border-b hover:bg-gray-50">

                        {{-- Código --}}
                        <td class="px-4 py-2 font-bold">
                            {{ $item->ordenanza332->codigo }}
                        </td>

                        {{-- Descripción --}}
                        <td class="px-4 py-2">
                            {{ Str::limit($item->ordenanza332->descripcion, 60) }}
                        </td>

                        {{-- Año --}}
                        <td class="px-4 py-2">
                            {{ $item->salarioMinimo->year }}
                        </td>

                        {{-- Salario --}}
                        <td class="px-4 py-2">
                            ${{ number_format($item->salarioMinimo->valor_usd, 2) }}
                        </td>

                        {{-- Porcentaje --}}
                        <td class="px-4 py-2">
                            {{ number_format($item->porcentaje, 2) }}%
                        </td>

                        {{-- Cálculo multa --}}
                        <td class="px-4 py-2 font-semibold">
                            ${{ number_format($item->calcularMulta(), 2) }}
                        </td>
  

                            <td class="p-2 border">
                                <div class="flex gap-3 justify-center">
                                    <a href="{{ route('porcentajes.show', $item->id) }}"
                                       class="text-green-600 hover:underline">Ver</a>

                                    <a href="{{ route('porcentajes.edit', $item->id) }}"
                                       class="text-blue-600 hover:underline">Editar</a>

                                    <button wire:click="confirmDelete({{ $item->id }})"
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
                        <h3 class="text-lg font-bold mb-4">¿Eliminar este Porcentaje de Multa?</h3>

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
        {{ $multas->links() }}
    </div>
</div>
