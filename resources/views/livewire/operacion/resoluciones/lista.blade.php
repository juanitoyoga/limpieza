@section('page-title', 'Resoluciones')
@section('page-description', 'Gestión de resoluciones')

<div class="bg-white p-6 rounded-xl shadow border border-gray-200">

    @if (session('message'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded flex items-center">
        <i class="fas fa-check-circle mr-2"></i> {{ session('message') }}
    </div>
    @endif

    @if (session('error'))
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    </div>
    @endif

    @error('global')
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ $message }}
    </div>
    @enderror

    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
        <div class="flex gap-3">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por código, título o tipo..."
                class="border px-4 py-2 rounded w-64">

            <select wire:model.live="filtroAuthStatus" class="border px-4 py-2 rounded">
                <option value="">Todos los estados</option>
                <option value="{{ \App\Models\Resolucion::ESTADO_PENDIENTE }}">Pendiente</option>
                <option value="{{ \App\Models\Resolucion::ESTADO_VERIFICADA }}">Verificada</option>
                <option value="{{ \App\Models\Resolucion::ESTADO_APROBADA }}">Aprobada</option>
                <option value="{{ \App\Models\Resolucion::ESTADO_RECHAZADA }}">Rechazada</option>
            </select>
        </div>

        <a href="{{ route('resoluciones.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Nueva Resolución
        </a>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="p-3 cursor-pointer" wire:click="sortBy('codigo')">
                    Código
                    @if($sortField === 'codigo') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                </th>
                <th class="p-3 cursor-pointer" wire:click="sortBy('titulo')">
                    Título
                    @if($sortField === 'titulo') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                </th>
                <th class="p-3">Tipo</th>
                <th class="p-3">Fecha</th>
                <th class="p-3">Estado</th>
                <th class="p-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resoluciones as $resolucion)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">{{ $resolucion->codigo }}</td>
                <td class="p-3">{{ $resolucion->titulo }}</td>
                <td class="p-3">{{ $resolucion->tipo }}</td>
                <td class="p-3">{{ $resolucion->fecha_resolucion?->format('d/m/Y') ?? '—' }}</td>
                <td class="p-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium text-white shadow-xs {{ $resolucion->estadoColor() }}">
                        {{ $resolucion->estadoLabel() }}
                    </span>
                </td>

                <td class="p-3 text-right">
                    <div class="flex items-center justify-end gap-3 text-base">
                        <a href="{{ route('resoluciones.show', $resolucion) }}"
                            title="Ver detalle"
                            class="text-gray-500 hover:text-gray-800 transition">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('resoluciones.edit', $resolucion) }}"
                            title="Editar"
                            class="text-blue-500 hover:text-blue-800 transition">
                            <i class="fas fa-pen"></i>
                        </a>

                        @if($resolucion->auth_status === \App\Models\Resolucion::ESTADO_PENDIENTE)
                        <a href="{{ route('resoluciones.verificar', $resolucion) }}"
                            title="Verificar (Dirigente)"
                            class="text-indigo-500 hover:text-indigo-800 transition">
                            <i class="fas fa-check"></i>
                        </a>
                        @elseif($resolucion->auth_status === \App\Models\Resolucion::ESTADO_VERIFICADA)
                        <a href="{{ route('resoluciones.aprobar', $resolucion) }}"
                            title="Aprobar (Presidente)"
                            class="text-green-500 hover:text-green-800 transition">
                            <i class="fas fa-check-double"></i>
                        </a>
                        @endif

                        @if(in_array($resolucion->auth_status, [\App\Models\Resolucion::ESTADO_PENDIENTE, \App\Models\Resolucion::ESTADO_VERIFICADA]))
                        <a href="{{ route('resoluciones.rechazar', $resolucion) }}"
                            title="Rechazar"
                            class="text-red-500 hover:text-red-800 transition">
                            <i class="fas fa-times-circle"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-6 text-center text-gray-500">No hay resoluciones registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $resoluciones->links() }}
    </div>

</div>