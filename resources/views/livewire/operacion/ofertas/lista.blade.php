@section('page-title', 'Ofertas de Servicios')
@section('page-description', 'Gestión de ofertas de servicios asociadas a resoluciones')
<div class="space-y-6">

    {{-- Título --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Ofertas</h1>

        <a href="{{ route('ofertas.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded">
            Nueva Oferta
        </a>
    </div>

    {{-- Filtros --}}
    <div class="bg-white p-4 rounded shadow flex items-center space-x-4">

        {{-- Búsqueda --}}
        <div class="flex-1">
            <input type="text"
                wire:model.live="search"
                placeholder="Buscar por código, título o proveedor..."
                class="w-full border rounded px-3 py-2">
        </div>

        {{-- Filtro por estado --}}
        <div>
            <select wire:model.live="filtroAuthStatus"
                class="border rounded px-3 py-2">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Verificada">Verificada</option>
                <option value="Aprobada">Aprobada</option>
                <option value="Rechazada">Rechazada</option>
            </select>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="bg-white p-4 rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-100">
                    <th class="p-2 cursor-pointer" wire:click="sortBy('codigo')">
                        Código
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('proveedor_id')">
                        Proveedor
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('resolucion_id')">
                        Resolución
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('monto_total')">
                        Monto Total
                    </th>
                    <th class="p-2 cursor-pointer" wire:click="sortBy('auth_status')">
                        Estado
                    </th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($ofertas as $oferta)
                <tr class="border-b">

                    {{-- Código --}}
                    <td class="p-2 font-semibold">
                        {{ $oferta->codigo }}
                    </td>

                    {{-- Proveedor --}}
                    <td class="p-2">
                        {{ $oferta->proveedor->nombre }}
                    </td>

                    {{-- Resolución --}}
                    <td class="p-2">
                        {{ $oferta->resolucion->codigo }}
                    </td>

                    {{-- Monto --}}
                    <td class="p-2">
                        {{ number_format($oferta->monto_total, 2) }} €
                    </td>

                    {{-- Estado --}}
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-white {{ $oferta->estadoColor() }}">
                            {{ $oferta->estadoLabel() }}
                        </span>
                    </td>

                    {{-- Acciones --}}
                    <td class="p-2 space-x-2">

                        {{-- PENDIENTE --}}
                        @if ($oferta->auth_status === 'Pendiente')
                        <a href="{{ route('ofertas.edit', $oferta) }}"
                            class="px-3 py-1 bg-yellow-500 text-white rounded">
                            Editar
                        </a>

                        <a href="{{ route('ofertas.servicios', $oferta) }}"
                            class="px-3 py-1 bg-blue-500 text-white rounded">
                            Servicios
                        </a>

                        <a href="{{ route('ofertas.verificar', $oferta) }}"
                            class="px-3 py-1 bg-green-600 text-white rounded">
                            Verificar
                        </a>
                        @endif

                        {{-- VERIFICADA --}}
                        @if ($oferta->auth_status === 'Verificada')
                        <a href="{{ route('ofertas.aprobar', $oferta) }}"
                            class="px-3 py-1 bg-green-600 text-white rounded">
                            Aprobar
                        </a>

                        <a href="{{ route('ofertas.rechazar', $oferta) }}"
                            class="px-3 py-1 bg-red-600 text-white rounded">
                            Rechazar
                        </a>
                        @endif

                        {{-- APROBADA / RECHAZADA --}}
                        @if (in_array($oferta->auth_status, ['Aprobada', 'Rechazada']))
                        <a href="{{ route('ofertas.servicios', $oferta) }}"
                            class="px-3 py-1 bg-gray-600 text-white rounded">
                            Ver Servicios
                        </a>
                        @endif

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