@section('page-title', $resolucion->codigo)
@section('page-description', 'Detalle de la resolución')

<div class="space-y-6">
    @if (session('message'))
    <div class="p-3 bg-green-100 text-green-800 rounded">
        {{ session('message') }}
    </div>
    @endif

    {{-- Info de la Resolución --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-bold">{{ $resolucion->titulo }}</h2>
                <p class="text-sm text-gray-500">Código: {{ $resolucion->codigo }}</p>
            </div>
            <a href="{{ route('resoluciones.edit', $resolucion) }}"
                class="text-blue-600 hover:underline text-sm">
                Editar resolución
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-4">
            <div><span class="text-gray-500">Tipo:</span> {{ $resolucion->tipo }}</div>
            <div><span class="text-gray-500">Fecha:</span> {{ $resolucion->fecha_resolucion?->format('d/m/Y') ?? '—' }}</div>
            <div><span class="text-gray-500">Estado Auth:</span> {{ $resolucion->auth_status }}</div>
            <div><span class="text-gray-500">Barrio ID:</span> {{ $resolucion->barrio_id }}</div>
            <div><span class="text-gray-500">Número de Firmas:</span> {{ $resolucion->numero_firmas ?? 0 }}</div>
            {{-- Antes usaba blockchain_tx_hash, campo inexistente en el modelo (el real es tx_hash) --}}
            <div><span class="text-gray-500">Hash Blockchain:</span>
                {{ $resolucion->tx_hash ? substr($resolucion->tx_hash, 0, 10) . '...' : '—' }}
            </div>
        </div>

        @if($resolucion->descripcion)
        <div class="text-sm border-t pt-3">
            <span class="text-gray-500 block mb-1">Descripción:</span>
            <p class="text-gray-700">{{ $resolucion->descripcion }}</p>
        </div>
        @endif
    </div>

    {{-- Gestión de Secciones --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Gestión de Sección</h3>

            <div class="flex gap-2">
                <button wire:click="openCreateParticipante"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
                    + Agregar participante
                </button>

                <button wire:click="openCreateServicio"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                    + Agregar servicio
                </button>
            </div>
        </div>

        {{-- Participantes --}}
        <div class="mb-6">
            <h4 class="font-semibold mb-3">Participantes</h4>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="p-3">Orden</th>
                        <th class="p-3">Firmante</th>
                        <th class="p-3">Documento</th>
                        <th class="p-3">Cargo</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantes as $participante)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ $participante->orden_firma }}</td>
                        <td class="p-3">{{ $participante->nombre_firmante }}</td>
                        <td class="p-3">{{ $participante->documento_identidad }}</td>
                        <td class="p-3">{{ $participante->cargo ?? '—' }}</td>
                        <td class="p-3 text-right space-x-2">
                            <button wire:click="openEditParticipante({{ $participante->id }})"
                                title="Editar"
                                class="text-blue-500 hover:text-blue-800 transition">
                                <i class="fas fa-pen"></i>
                            </button>
                            {{-- Antes: confirmDelete($id) sin tipo, causaba que borrar un
                                 servicio intentara buscar ese ID entre los participantes --}}
                            <button wire:click="confirmDelete({{ $participante->id }}, 'participante')"
                                title="Eliminar"
                                class="text-red-500 hover:text-red-800 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No hay participantes registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Servicios --}}
        <div>
            <h4 class="font-semibold mb-3">Servicios</h4>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="p-3">Servicio</th>
                        <th class="p-3">Cantidad</th>
                        <th class="p-3">Prioridad</th>
                        <th class="p-3">Costo U.</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicios as $servicio)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">
                            {{ $servicio->catalogoServicio?->nombre ?? '—' }}
                        </td>
                        <td class="p-3">{{ $servicio->cantidad }}</td>
                        <td class="p-3">{{ $servicio->prioridad ?? '—' }}</td>
                        <td class="p-3">
                            ${{ number_format((float) $servicio->costo_unitario, 2) }}
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <button wire:click="openEditServicio({{ $servicio->id }})"
                                title="Editar"
                                class="text-blue-500 hover:text-blue-800 transition">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $servicio->id }}, 'servicio')"
                                title="Eliminar"
                                class="text-red-500 hover:text-red-800 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            No hay servicios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Participante --}}
    @if($showParticipanteModal)
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-lg">
            <h3 class="text-lg font-bold mb-4">
                {{ $participanteId ? 'Editar Participante' : 'Nuevo Participante' }}
            </h3>

            <form wire:submit.prevent="saveParticipante" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Firmante</label>
                    <input type="text" wire:model="participante_nombre_firmante"
                        class="w-full border px-4 py-2 rounded">
                    @error('participante_nombre_firmante')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Documento Identidad</label>
                    <input type="text" wire:model="participante_documento_identidad"
                        class="w-full border px-4 py-2 rounded">
                    @error('participante_documento_identidad')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <input type="text" wire:model="participante_cargo"
                        class="w-full border px-4 py-2 rounded">
                    @error('participante_cargo')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Orden de Firma</label>
                        <input type="number" wire:model="participante_orden_firma" min="1"
                            class="w-full border px-4 py-2 rounded">
                        @error('participante_orden_firma')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">User ID (Opcional)</label>
                        <input type="number" wire:model="participante_user_id"
                            class="w-full border px-4 py-2 rounded">
                        @error('participante_user_id')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button"
                        wire:click="$set('showParticipanteModal', false)"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Servicio --}}
    @if($showServicioModal)
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-lg">
            <h3 class="text-lg font-bold mb-4">
                {{ $servicioId ? 'Editar Servicio' : 'Nuevo Servicio' }}
            </h3>

            <form wire:submit.prevent="saveServicio" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catálogo Servicio</label>
                    <input type="number" wire:model="servicio_catalogo_servicio_id"
                        class="w-full border px-4 py-2 rounded">
                    @error('servicio_catalogo_servicio_id')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                    <input type="number" wire:model="servicio_cantidad" min="1"
                        class="w-full border px-4 py-2 rounded">
                    @error('servicio_cantidad')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                    <select wire:model="servicio_prioridad" class="w-full border px-4 py-2 rounded">
                        <option value="">-- Seleccione --</option>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                    @error('servicio_prioridad')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea wire:model="servicio_observaciones"
                        class="w-full border px-4 py-2 rounded"></textarea>
                    @error('servicio_observaciones')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select wire:model="servicio_estado" class="w-full border px-4 py-2 rounded">
                            <option value="">Pendiente</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Verificada">Verificada</option>
                            <option value="Aprobada">Aprobada</option>
                            <option value="Rechazada">Rechazada</option>
                        </select>
                        @error('servicio_estado')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Costo Unitario</label>
                        <input type="number" step="0.01" wire:model="servicio_costo_unitario"
                            class="w-full border px-4 py-2 rounded">
                        @error('servicio_costo_unitario')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button"
                        wire:click="$set('showServicioModal', false)"
                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Confirmar Borrado --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-lg">
            <h3 class="text-lg font-bold mb-3">
                ¿Eliminar {{ $deleteType === 'servicio' ? 'servicio' : 'participante' }}?
            </h3>
            <p class="text-gray-600 mb-6">Esta acción no se puede deshacer.</p>
            <div class="flex justify-end gap-3">
                <button wire:click="$set('confirmingDelete', false)"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Cancelar
                </button>
                <button wire:click="delete"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>