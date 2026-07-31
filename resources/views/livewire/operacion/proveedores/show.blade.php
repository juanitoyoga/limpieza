@section('page-title', $proveedor->razon_social)
@section('page-description', 'Detalle del proveedor')

<div class="space-y-6">

    @if (session('message'))
    <div class="p-3 bg-green-100 text-green-800 rounded">
        {{ session('message') }}
    </div>
    @endif

    {{-- Info del proveedor --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-xl font-bold">{{ $proveedor->razon_social }}</h2>
            <a href="{{ route('proveedores.edit', $proveedor) }}"
                class="text-blue-600 hover:underline text-sm">Editar proveedor</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">RUC:</span> {{ $proveedor->ruc }}</div>
            <div><span class="text-gray-500">Tipo de servicio:</span> {{ $proveedor->tipo_servicio ?? '—' }}</div>
            <div><span class="text-gray-500">Representante legal:</span> {{ $proveedor->representante_legal ?? '—' }}</div>
            <div><span class="text-gray-500">Email:</span> {{ $proveedor->email ?? '—' }}</div>
            <div><span class="text-gray-500">Teléfono:</span> {{ $proveedor->telefono ?? '—' }}</div>
            <div>
                <span class="px-2 py-1 rounded text-white text-xs {{ $proveedor->estadoColor() }}">
                    {{ $proveedor->estadoLabel() }}
                </span>
            </div>
        </div>
    </div>

    {{-- Contactos --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Contactos</h3>
            <button wire:click="openCreateContacto"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                + Agregar contacto
            </button>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Cargo</th>
                    <th class="p-3">Teléfono</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Principal</th>
                    <th class="p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contactos as $contacto)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $contacto->nombre }}</td>
                    <td class="p-3">{{ $contacto->cargo ?? '—' }}</td>
                    <td class="p-3">{{ $contacto->telefono ?? '—' }}</td>
                    <td class="p-3">{{ $contacto->email ?? '—' }}</td>
                    <td class="p-3">
                        @if($contacto->es_principal)
                        <span class="px-2 py-1 rounded bg-blue-500 text-white text-xs">Principal</span>
                        @endif
                    </td>
                    <td class="p-3 text-right space-x-2">
                        <button wire:click="openEditContacto({{ $contacto->id }})"
                            class="text-blue-600 hover:underline">Editar</button>
                        <button wire:click="confirmDelete({{ $contacto->id }})"
                            class="text-red-600 hover:underline">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">No hay contactos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Crear/Editar Contacto --}}
    @if($showContactoModal)
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-lg">
            <h3 class="text-lg font-bold mb-4">
                {{ $contactoId ? 'Editar Contacto' : 'Nuevo Contacto' }}
            </h3>

            <form wire:submit.prevent="saveContacto" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" wire:model="nombre" class="w-full border px-4 py-2 rounded">
                    @error('nombre') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <input type="text" wire:model="cargo" class="w-full border px-4 py-2 rounded"
                        placeholder="Ej. Jefe de obra, Responsable de campo...">
                    @error('cargo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" wire:model="telefono" class="w-full border px-4 py-2 rounded">
                        @error('telefono') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full border px-4 py-2 rounded">
                        @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="es_principal" id="es_principal" class="rounded">
                    <label for="es_principal" class="text-sm text-gray-700">Marcar como contacto principal</label>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showContactoModal', false)"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Confirmar Borrado --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-lg">
            <h3 class="text-lg font-bold mb-3">¿Eliminar contacto?</h3>
            <p class="text-gray-600 mb-6">Esta acción no se puede deshacer.</p>
            <div class="flex justify-end gap-3">
                <button wire:click="$set('confirmingDelete', false)"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                <button wire:click="deleteContacto"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
            </div>
        </div>
    </div>
    @endif
</div>