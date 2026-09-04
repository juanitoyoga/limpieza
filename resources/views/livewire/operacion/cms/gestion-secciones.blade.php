@section('page-title', 'CMS — Definición de Secciones e Ítems')
@section('page-description', 'Banners, noticias, auspiciadores y demás contenido visual de la plataforma')


<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex justify-between items-center bg-white p-4 rounded-lg border shadow-sm">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Especificaciones de Secciones</h2>
            <p class="text-xs text-gray-500">Administra las reglas de la plantilla y los slots de contenido disponibles.</p>
        </div>
        <button wire:click="openModalSeccion" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
            + Nueva Sección
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Panel Izquierdo: Lista de Secciones --}}
        <div class="bg-white border rounded-lg shadow-sm p-4 space-y-3">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Secciones Configuradas</h3>

            <div class="space-y-2 max-h-[600px] overflow-y-auto">
                @forelse($secciones as $sec)
                <div wire:click="selectSeccion({{ $sec->id }})"
                    class="p-3 rounded-md border cursor-pointer transition-all {{ $seccionSeleccionadaId === $sec->id ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-gray-300 bg-white' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-800 text-sm">{{ $sec->area }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-gray-100 text-gray-600 font-mono">{{ $sec->version_spec }}</span>
                    </div>
                    <div class="mt-2 flex justify-between items-center text-[11px] text-gray-500">
                        <span>Tipo: <strong>{{ $sec->multiplicidad }}</strong></span>
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-medium">
                            {{ $sec->items_count }} / {{ $sec->max_items ?? '∞' }} ítems
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-4 text-center">Sin secciones registradas.</p>
                @endforelse
            </div>
        </div>

        {{-- Panel Derecho: Campos e Ítems --}}
        <div class="md:col-span-2 bg-white border rounded-lg shadow-sm p-5">
            @if($seccionSeleccionada)
            <div class="flex justify-between items-start border-b pb-4 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800">{{ $seccionSeleccionada->area }}</h3>
                    <p class="text-xs text-gray-500">{{ $seccionSeleccionada->descripcion ?? 'Sin descripción de la sección.' }}</p>
                </div>

                <div class="flex bg-gray-100 p-1 rounded-lg text-xs">
                    <button wire:click="$set('pestanaActiva', 'campos')"
                        class="px-3 py-1.5 rounded-md font-medium transition-all {{ $pestanaActiva === 'campos' ? 'bg-white shadow text-blue-600' : 'text-gray-600' }}">
                        Estructura de Campos ({{ $seccionSeleccionada->camposDefinicion->count() }})
                    </button>
                    <button wire:click="$set('pestanaActiva', 'items')"
                        class="px-3 py-1.5 rounded-md font-medium transition-all {{ $pestanaActiva === 'items' ? 'bg-white shadow text-blue-600' : 'text-gray-600' }}">
                        Ítems / Slots ({{ $seccionSeleccionada->items->count() }})
                    </button>
                </div>
            </div>

            {{-- TAB: ESTRUCTURA DE CAMPOS --}}
            @if($pestanaActiva === 'campos')
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs text-gray-500">Campos que componen cada ítem de esta sección.</p>
                <button wire:click="openModalCampo()" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md">
                    + Agregar Campo
                </button>
            </div>

            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b">
                        <th class="p-2 font-semibold">Ord</th>
                        <th class="p-2 font-semibold">Clave</th>
                        <th class="p-2 font-semibold">Etiqueta</th>
                        <th class="p-2 font-semibold">Tipo</th>
                        <th class="p-2 font-semibold">Reglas</th>
                        <th class="p-2 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($seccionSeleccionada->camposDefinicion as $campo)
                    <tr>
                        <td class="p-2 font-mono text-gray-400">{{ $campo->orden }}</td>
                        <td class="p-2 font-mono font-medium text-blue-600">{{ $campo->clave }}</td>
                        <td class="p-2 font-medium text-gray-800">{{ $campo->etiqueta }}</td>
                        <td class="p-2"><span class="px-1.5 py-0.5 rounded bg-gray-100 font-mono text-[10px]">{{ $campo->tipo_dato }}</span></td>
                        <td class="p-2 text-gray-500">
                            @if($campo->tipo_dato === 'imagen')
                            {{ $campo->imagen_ancho }}x{{ $campo->imagen_alto }}px
                            @else
                            —
                            @endif
                        </td>
                        <td class="p-2 text-right space-x-2">
                            <button wire:click="openModalCampo({{ $campo->id }})" class="text-blue-600 hover:underline">Editar</button>
                            <button wire:click="deleteCampo({{ $campo->id }})" wire:confirm="¿Eliminar campo?" class="text-red-600 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-400">Sin campos definidos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- TAB: SLOTS DE ÍTEMS --}}
            @elseif($pestanaActiva === 'items')
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs text-gray-500">
                    Slots de contenido activos (Máximo: <strong>{{ $seccionSeleccionada->max_items ?? 'Sin límite' }}</strong>).
                </p>
                <button wire:click="crearItemSlot" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md">
                    + Generar Ítem Slot
                </button>
            </div>

            @if(session('item_error'))
            <div class="mb-3 p-2.5 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md">
                {{ session('item_error') }}
            </div>
            @endif

            @if(session('item_msg'))
            <div class="mb-3 p-2.5 bg-green-50 border border-green-200 text-green-700 text-xs rounded-md">
                {{ session('item_msg') }}
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($seccionSeleccionada->items as $item)
                <div class="p-3 border rounded-lg bg-gray-50 space-y-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-bold text-gray-700">Orden #{{ $item->orden }}</span>
                            <p class="text-[10px] font-mono text-gray-500">{{ $item->identificador }}</p>
                        </div>
                        <button wire:click="deleteItemSlot({{ $item->id }})"
                            wire:confirm="¿Eliminar este slot de ítem?"
                            class="text-red-500 hover:text-red-700 text-xs">
                            ✕
                        </button>
                    </div>

                    <div class="text-[11px] text-gray-600 bg-white p-2 rounded border">
                        <p><strong>Publicado:</strong>
                            @if($item->versionPublicada)
                            v{{ $item->versionPublicada->numero_version }}
                            @else
                            <span class="text-amber-600">Sin versión activa</span>
                            @endif
                        </p>
                        <p><strong>Última propuesta:</strong>
                            @if($item->ultimaVersion)
                            v{{ $item->ultimaVersion->numero_version }} ({{ $item->ultimaVersion->auth_status }})
                            @else
                            <span class="text-gray-400">N/A</span>
                            @endif
                        </p>
                    </div>

                    <div class="pt-1">
                        <a href="{{ route('cms.proponer', ['seccionId' => $seccionSeleccionada->id, 'itemId' => $item->id]) }}"
                            class="block w-full text-center py-1.5 bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 text-xs font-medium rounded shadow-sm">
                            Editar / Proponer Contenido
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-2 py-8 text-center text-gray-400 text-xs">
                    Sin slots de ítems generados para esta sección.
                </div>
                @endforelse
            </div>
            @endif
            @else
            <div class="py-12 text-center text-gray-400 text-xs">
                Selecciona una sección para administrar su configuración.
            </div>
            @endif
        </div>
    </div>

</div>