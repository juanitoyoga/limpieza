
@section('page-title', 'Nominaciones')
    
@section('page-description', 'Mantenimiento de Registros')

    <!-- Tabla de registros -->
    <div class="overflow-x-auto">
        @if(session('message'))
            <div class="alert alert-{{ session('message.type') }}">
                {{ session('message.text') }}
            </div>
        @endif

        <div class="gap-3">

            <input type="text"
                   wire:model.debounce.300ms="search"
                   placeholder="Buscar nominacion..."
                   class="border px-4 py-2 rounded flex-1 min-w-[200px]">
        
            <select wire:model.live="perPage"
                   class="border px-10 py-2 rounded bg-white">
           
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        
            <button @click="scroll = !scroll"
                    class="bg-gray-600 text-white px-4 py-2 rounded">
                Scroll
            </button>
        
            <a href="{{ route('nominations.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                + Crear
            </a>
        </div>
      

        <table class="w-full">
            <thead class="bg-gray-50">

                <tr>
                    @foreach([
                        'nominator->last_name' => 'Responsable',
                        'candidate->last_name' => 'Nombramiento',
                        'numero_tramite'       => 'Nro. Trámite',
                        'estado'               => 'Estado',
                        'created_at'           => 'Fecha Trámite',
                        ] as $field => $label)

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer"
                            wire:click="sortBy('{{ $field }}')">
                            <div class="flex items-center justify-between">
                                {{ $label }}

                                @if ($sortField === $field)
                                    <span>
                                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y">
                @forelse($nominations as $nomination)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">
                            {{ $nomination->nominator->first_name }} {{ $nomination->nominator->last_name }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $nomination->candidate->first_name }} {{ $nomination->candidate->last_name }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $nomination->numero_tramite }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-white text-xs
                                {{ $nomination->estadoColor() }}">
                                {{ $nomination->estadoLabel() }}
                            </span>
                        </td>
 
                        
                        <td class="px-6 py-4 text-sm">
                            {{ $nomination->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-3">
                                {{-- La opción 'Ver' siempre está visible --}}
                                <a href="{{ route('nominations.show', $nomination->id) }}"
                                   class="text-green-600 hover:underline">Ver</a>
                            
                                @if($nomination->estado === 'propuesta')
                                    {{-- Estado Propuesta: Ver, Verificar y Editar --}}
                                    <a href="{{ route('nominations.verificar', $nomination->id) }}" 
                                       class="text-orange-600 hover:underline">Verificar</a>
                            
                                    <a href="{{ route('nominations.edit', $nomination->id) }}"
                                       class="text-blue-600 hover:underline">Editar</a>
                            
                                @elseif($nomination->estado === 'verificada')
                                    {{-- Estado Verificada: Ver y Aprobar y Rechazar--}}
                                    <a href="{{ route('nominations.aprobar', $nomination->id) }}" 
                                       class="text-purple-600 hover:underline">Aprobar</a>
                                    <a href="{{ route('nominations.rechazar', $nomination->id) }}" 
                                    class="text-purple-600 hover:underline">Rechazar</a>                            
                                @elseif($nomination->estado === 'aprobada')
                                    {{-- Estado Aprobada: Solo Ver (ya incluido arriba) --}}
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No existen registros
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $nominations->links() }}
    </div>

</div>
