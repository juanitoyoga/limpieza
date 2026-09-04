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
            <div><span class="text-gray-500">Actividad:</span> {{ $proveedor->actividad ?? '—' }}</div>
            <div><span class="text-gray-500">Nombre Comercial:</span> {{ $proveedor->nombre_comercial ?? '—' }}</div>
            <div><span class="text-gray-500">Email:</span> {{ $proveedor->email ?? '—' }}</div>
            <div><span class="text-gray-500">Teléfono:</span> {{ $proveedor->telefono ?? '—' }}</div>
            <div>{{ $proveedor->direccion ?? '—' }}</div>
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
                    <th class="p-3">App</th>
                    <th class="p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contactos as $contacto)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $contacto->nombre_completo }}</td>
                    <td class="p-3">{{ $contacto->cargo ?? '—' }}</td>
                    <td class="p-3">{{ $contacto->phone ?? '—' }}</td>
                    <td class="p-3">{{ $contacto->email ?? '—' }}</td>
                    <td class="p-3">
                        @if($contacto->es_principal)
                        <span class="px-2 py-1 rounded bg-blue-500 text-white text-xs">Principal</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($contacto->usa_app && $contacto->contratista)
                        <span class="px-2 py-1 rounded bg-green-500 text-white text-xs">Contratista</span>
                        @elseif($contacto->usa_app)
                        <span class="px-2 py-1 rounded bg-yellow-500 text-white text-xs">Pendiente</span>
                        @endif
                    </td>
                    <td class="p-3 text-right space-x-2">
                        <button wire:click="openEditContacto({{ $contacto->id }})"
                            title="Editar"
                            class="text-gray-500 hover:text-gray-800 transition">
                            <i class="fas fa-eye"></i></button>
                        <button wire:click="confirmDelete({{ $contacto->id }})"
                            title="Eliminar"
                            class="text-red-600 hover:text-red-800 transition">
                            <i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">No hay contactos registrados.</td>
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
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres</label>
                        <input type="text" wire:model="first_name" class="w-full border px-4 py-2 rounded">
                        @error('first_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                        <input type="text" wire:model="last_name" class="w-full border px-4 py-2 rounded">
                        @error('last_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>
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
                        <input type="text" wire:model="phone" class="w-full border px-4 py-2 rounded">
                        @error('phone') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
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

                <hr class="my-2">

                <div>
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            wire:model.live="usa_app"
                            id="usa_app"
                            class="rounded"
                            @if($yaGenerado) disabled @endif>
                        <label for="usa_app" class="text-sm text-gray-700 font-medium">
                            Dar acceso a la app móvil (rol Contratista)
                        </label>
                    </div>

                    @if($yaGenerado)
                    <p class="text-xs text-green-700 mt-1">
                        ✓ Este contacto ya tiene una cuenta generada. Para revocar el acceso,
                        usa la pantalla de sesiones del usuario en vez de desmarcar aquí.
                    </p>
                    @elseif($usa_app)
                    <p class="text-xs text-gray-500 mt-1">
                        Se creará un usuario automáticamente y se enviará la contraseña
                        temporal al email indicado arriba.
                    </p>
                    @endif
                </div>

                @if($usa_app && !$yaGenerado)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo ID <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="tipo_id" class="w-full border px-4 py-2 rounded">
                            <option value="CEDULA">Cédula</option>
                            <option value="RUC">RUC</option>
                            <option value="PASAPORTE">Pasaporte</option>
                        </select>
                        @error('tipo_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Número <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nro_id" maxlength="20" class="w-full border px-4 py-2 rounded">
                        @error('nro_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-500 -mt-2">Requerido para generar la cuenta de usuario.</p>
                @endif

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