@section('page-title', 'Ofertas de Servicios')
@section('page-description', 'Gestión de ofertas de servicios asociadas a resoluciones')

<div class="space-y-6">


    {{-- Búsqueda + Filtro + Botón, todo en una sola línea --}}
    <div class="bg-white p-4 rounded shadow flex flex-wrap items-center gap-3">

        <div class="flex-1 min-w-[200px]">
            <input type="text"
                wire:model.live.debounce.400ms="search"
                placeholder="Buscar por código, título o proveedor..."
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <select wire:model.live="filtroAuthStatus" class="border rounded px-3 py-2">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Verificada">Verificada</option>
                <option value="Aprobada">Aprobada</option>
                <option value="Rechazada">Rechazada</option>
            </select>
        </div>

        <a href="{{ route('ofertas.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 whitespace-nowrap">
            <i class="fas fa-plus mr-1"></i> Nueva Oferta
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white p-4 rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-100">
                    <th class="p-2 cursor-pointer" wire:click="sortBy('codigo')">
                        Código
                        @if ($sortField === 'codigo') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i> @endif
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('titulo')">
                        Título
                        @if ($sortField === 'titulo') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i> @endif
                    </th>
                    <th class="p-2">Proveedor</th>
                    <th class="p-2">Resolución</th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('fecha_presentacion')">
                        Fecha Presentación
                        @if ($sortField === 'fecha_presentacion') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i> @endif
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('monto_total')">
                        Monto Total
                        @if ($sortField === 'monto_total') <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-xs"></i> @endif
                    </th>
                    <th class="p-2 text-center cursor-pointer" wire:click="sortBy('auth_status')">
                        Estado
                    </th>
                    <th class="p-2 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($ofertas as $oferta)
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-2 font-semibold">{{ $oferta->codigo }}</td>

                    <td class="p-2">{{ $oferta->titulo }}</td>

                    <td class="p-2">{{ $oferta->proveedor->razon_social }}</td>

                    <td class="p-2">{{ $oferta->resolucion->codigo }}</td>

                    <td class="p-2">{{ $oferta->fecha_presentacion?->format('d/m/Y') ?? '—' }}</td>

                    <td class="p-2">${{ number_format($oferta->monto_total, 2) }}</td>

                    {{-- Estado como icono --}}
                    <td class="p-2 text-center">
                        @php
                        $estadoIcono = match($oferta->auth_status) {
                        'Pendiente' => 'fa-clock',
                        'Verificada' => 'fa-magnifying-glass',
                        'Aprobada' => 'fa-circle-check',
                        'Rechazada' => 'fa-circle-xmark',
                        default => 'fa-circle-question',
                        };
                        @endphp
                        <span title="{{ $oferta->estadoLabel() }}"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white {{ $oferta->estadoColor() }}">
                            <i class="fas {{ $estadoIcono }}"></i>
                        </span>
                    </td>

                    {{-- Acciones como iconos --}}
                    <td class="p-2">
                        <div class="flex items-center justify-center gap-3">

                            <a href="{{ route('ofertas.show', $oferta) }}"
                                title="Ver Detalle"
                                class="text-gray-600 hover:text-gray-900 transition">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if ($oferta->auth_status === 'Pendiente')
                            <a href="{{ route('ofertas.servicios', $oferta) }}"
                                title="Servicios"
                                class="text-blue-500 hover:text-blue-800 transition">
                                <i class="fas fa-list-check"></i>
                            </a>

                            <a href="{{ route('ofertas.formapago', $oferta) }}"
                                title="Forma de Pago"
                                class="text-green-600 hover:text-green-800 transition">
                                <i class="fas fa-sack-dollar"></i>
                            </a>

                            <a href="{{ route('ofertas.verificar', $oferta) }}"
                                title="Verificar"
                                class="text-yellow-600 hover:text-yellow-800 transition">
                                <i class="fas fa-magnifying-glass"></i>
                            </a>
                            @endif

                            @if ($oferta->auth_status === 'Verificada')
                            <a href="{{ route('ofertas.aprobar', $oferta) }}"
                                title="Aprobar"
                                class="text-green-600 hover:text-green-800 transition">
                                <i class="fas fa-check"></i>
                            </a>

                            <a href="{{ route('ofertas.rechazar', $oferta) }}"
                                title="Rechazar"
                                class="text-red-500 hover:text-red-800 transition">
                                <i class="fas fa-xmark"></i>
                            </a>
                            @endif

                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $ofertas->links() }}
        </div>
    </div>

</div>