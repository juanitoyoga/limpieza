@section('page-title', 'Contratos de Servicio')
@section('page-description', 'Gestión de contratos con proveedores')

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

    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
        <div class="flex gap-3">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por código o título..."
                class="border px-4 py-2 rounded w-64">

            <select wire:model.live="filtroAuthStatus" class="border px-4 py-2 rounded">
                <option value="">Todos los estados</option>
                <option value="{{ \App\Models\ContratoServicio::ESTADO_PENDIENTE }}">Pendiente</option>
                <option value="{{ \App\Models\ContratoServicio::ESTADO_VERIFICADA }}">Verificada</option>
                <option value="{{ \App\Models\ContratoServicio::ESTADO_APROBADA }}">Aprobada</option>
                <option value="{{ \App\Models\ContratoServicio::ESTADO_RECHAZADA }}">Rechazada</option>
                <option value="{{ \App\Models\ContratoServicio::ESTADO_RESCINDIDO }}">Rescindido</option>
                <option value="{{ \App\Models\ContratoServicio::ESTADO_LIQUIDADO }}">Liquidado</option>
            </select>
        </div>
        <div class="flex flex-col">
            <label class="text-sm text-gray-700 mb-1">Filas por página</label>
            <select wire:model.live="perPage" class="border rounded px-3 py-2 w-28">
                @foreach ($opcionesPerPage as $opcion)
                <option value="{{ $opcion }}">{{ $opcion }}</option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('contratos-servicios.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            + Contrato Nuevo
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
                <th class="p-3">Proveedor</th>
                <th class="p-3">Monto</th>
                <th class="p-3">Estado</th>
                <th class="p-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contratos as $contrato)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold">{{ $contrato->codigo }}</td>
                <td class="p-3">{{ $contrato->titulo }}</td>
                <td class="p-3">{{ $contrato->proveedor->nombre_comercial ?? '—' }}</td>
                <td class="p-3">${{ number_format($contrato->monto_total, 2) }}</td>
                <td class="p-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium text-white shadow-xs {{ $contrato->estadoColor() }}">
                        {{ $contrato->estadoLabel() }}
                    </span>
                </td>

                <td class="p-3 text-right">
                    <div class="flex items-center justify-end gap-3 text-base">
                        <a href="{{ route('contratos-servicios.show', $contrato) }}"
                            title="Ver detalle" class="text-gray-500 hover:text-gray-800 transition">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_PENDIENTE)
                        <a href="{{ route('contratos-servicios.verificar', $contrato) }}"
                            title="Verificar (Dirigente)" class="text-indigo-500 hover:text-indigo-800 transition">
                            <i class="fas fa-check"></i>
                        </a>
                        @elseif($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_VERIFICADA)
                        <a href="{{ route('contratos-servicios.aprobar', $contrato) }}"
                            title="Aprobar (Presidente)" class="text-green-500 hover:text-green-800 transition">
                            <i class="fas fa-check-double"></i>
                        </a>
                        @endif

                        @if(in_array($contrato->auth_status, [\App\Models\ContratoServicio::ESTADO_PENDIENTE, \App\Models\ContratoServicio::ESTADO_VERIFICADA]))
                        <a href="{{ route('contratos-servicios.rechazar', $contrato) }}"
                            title="Rechazar" class="text-red-500 hover:text-red-800 transition">
                            <i class="fas fa-times-circle"></i>
                        </a>
                        @endif

                        @if($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_APROBADA)
                        <a href="{{ route('contratos-servicios.asignarpersonal', $contrato) }}"
                            title="Asignar Personal" class="text-green-500 hover:text-green-800 transition">
                            <i class="fas fa-user-plus"></i>
                        </a>
                        <a href="{{ route('contratos-servicios.rescindir', $contrato) }}"
                            title="Rescindir (Presidente)" class="text-orange-500 hover:text-orange-800 transition">
                            <i class="fas fa-ban"></i>
                        </a>
                        <a href="{{ route('contratos-servicios.liquidar', $contrato) }}"
                            title="Liquidar (Presidente)" class="text-blue-500 hover:text-blue-800 transition">
                            <i class="fas fa-flag-checkered"></i>
                        </a>
                        @endif
                        @if($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_APROBADA)
                        <a href="{{ route('contratos-servicios.gestion-ordenes-pago', $contrato) }}"
                            title="Órdenes de Pago"
                            class="text-green-600 hover:text-green-800 transition">
                            <i class="fas fa-sack-dollar"></i>
                        </a>
                        @endif

                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-6 text-center text-gray-500">No hay contratos registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $contratos->links() }}</div>
</div>