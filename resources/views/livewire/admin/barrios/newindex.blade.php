<div class="bg-white rounded-xl shadow-lg overflow-hidden">

    <!-- Tabla de registros -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($fields as $field => $config)
                        @if($config['listable'] ?? true)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('{{ $field }}')">
                            <div class="flex items-center gap-1">
                                {{ $config['label'] }}
                                @if($sortField === $field)
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort text-gray-300"></i>
                                @endif
                            </div>
                        </th>
                        @endif
                    @endforeach
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($records as $record)
                <tr class="hover:bg-gray-50 transition">
                    @foreach($fields as $field => $config)
                        @if($config['listable'] ?? true)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($config['type'] === 'image')
                                <img src="{{ $record->$field }}" alt="{{ $config['label'] }}" class="w-10 h-10 rounded object-cover">
                            @elseif($config['type'] === 'boolean')
                                <span class="px-2 py-1 text-xs rounded-full {{ $record->$field ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $record->$field ? 'Sí' : 'No' }}
                                </span>
                            @else
                                {{ $record->$field }}
                            @endif
                        </td>
                        @endif
                    @endforeach
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <button wire:click="setOperation('show', {{ $record->id }})"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                    title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button wire:click="setOperation('edit', {{ $record->id }})"
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition"
                                    title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="setOperation('delete', {{ $record->id }})"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($fields) + 1 }}" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center gap-3">
                            <i class="fas fa-inbox text-4xl text-gray-300"></i>
                            <p>No se encontraron {{ strtolower($modelNamePlural) }}</p>
                            @if($search || count(array_filter($filters)))
                            <button wire:click="$set('search', '')"
                                    class="text-{{ $uiConfig['primaryColor'] }}-600 hover:text-{{ $uiConfig['primaryColor'] }}-800">
                                Limpiar filtros
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($records->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $records->links() }}
    </div>
    @endif
</div>