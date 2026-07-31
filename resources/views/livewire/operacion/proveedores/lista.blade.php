@section('page-title', 'Proveedores')
@section('page-description', 'Gestión de proveedores')

<div class="bg-white p-6 rounded-xl shadow border border-gray-200">

    @if (session('message'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('message') }}
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
        <div class="flex gap-3">
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por razón social o RUC..."
                class="border px-4 py-2 rounded w-64">

            <select wire:model.live="filtroEstado" class="border px-4 py-2 rounded">
                <option value="">Todos los estados</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>

        <a href="{{ route('proveedores.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            + Nuevo Proveedor
        </a>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="p-3 cursor-pointer" wire:click="sortBy('razon_social')">
                    Razón Social
                    @if($sortField === 'razon_social') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                </th>
                <th class="p-3 cursor-pointer" wire:click="sortBy('ruc')">
                    RUC
                    @if($sortField === 'ruc') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                </th>
                <th class="p-3">Tipo de servicio</th>
                <th class="p-3">Estado</th>
                <th class="p-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proveedores as $proveedor)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">{{ $proveedor->razon_social }}</td>
                <td class="p-3">{{ $proveedor->ruc }}</td>
                <td class="p-3">{{ $proveedor->tipo_servicio ?? '—' }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded text-white text-xs {{ $proveedor->estadoColor() }}">
                        {{ $proveedor->estadoLabel() }}
                    </span>
                </td>
                <td class="p-3 text-right space-x-2">
                    <a href="{{ route('proveedores.show', $proveedor) }}" class="text-gray-700 hover:underline">Ver</a>
                    <a href="{{ route('proveedores.edit', $proveedor) }}"
                        class="text-blue-600 hover:underline">Editar</a>
                    <button wire:click="confirmDelete({{ $proveedor->id }})"
                        class="text-red-600 hover:underline">Eliminar</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-6 text-center text-gray-500">No hay proveedores registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $proveedores->links() }}
    </div>

    {{-- Modal de confirmación --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-lg">
            <h3 class="text-lg font-bold mb-3">¿Eliminar proveedor?</h3>
            <p class="text-gray-600 mb-6">Esta acción no se puede deshacer.</p>
            <div class="flex justify-end gap-3">
                <button wire:click="$set('confirmingDelete', false)"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                <button wire:click="delete"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
            </div>
        </div>
    </div>
    @endif
</div>