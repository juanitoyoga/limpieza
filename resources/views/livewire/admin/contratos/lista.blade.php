@section('page-title', 'Contratos')
@section('page-description', 'Gestión de Contratos')

<div x-data="{ scroll: false }">

    {{-- Mensajes --}}
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
            placeholder="Buscar contrato, barrio, estado..."
            class="border px-4 py-2 rounded flex-1 min-w-[200px]">

        <select wire:model.live="perPage"
            class="border px-4 py-2 rounded bg-white">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>

        <a href="{{ route('contratos.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Nuevo Contrato
        </a>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    @foreach([
                    'id' => 'ID',
                    'numero_contrato' => 'N° Contrato',
                    'barrio_id' => 'Barrio',
                    'monto_total' => 'Monto',
                    'estado' => 'Estado',
                    'fecha_inicio' => 'Inicio',
                    'fecha_fin' => 'Fin',
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

                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse($contratos as $contrato)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $contrato->id }}</td>
                    <td class="px-6 py-4">{{ $contrato->numero_contrato }}</td>
                    <td class="px-6 py-4">{{ $contrato->barrio->nombre ?? '—' }}</td>
                    <td class="px-6 py-4">$ {{ number_format($contrato->monto_total, 2) }}</td>

                    {{-- Estado --}}
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs text-white {{ $contrato->estadoColor() }}">
                            {{ $contrato->estadoLabel() }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $contrato->fecha_inicio?->format('d/m/Y') }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $contrato->fecha_fin?->format('d/m/Y') }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex gap-3 justify-center">
                            <a href="{{ route('contratos.edit', $contrato->id) }}"
                                class="text-blue-600 hover:underline">
                                <i class="fas fa-edit"></i> Editar
                            </a>

                            <button wire:click="confirmDelete({{ $contrato->id }})"
                                class="text-red-600 hover:underline">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                        <i class="fas fa-file-contract text-3xl mb-2 block"></i>
                        No se encontraron contratos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
        {{ $contratos->links() }}
    </div>

    {{-- Modal eliminar --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl max-w-sm w-full mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-bold text-gray-900">¿Eliminar este contrato?</h3>
            </div>

            <p class="text-gray-600 mb-6 text-sm">
                Esta acción no se puede deshacer.
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