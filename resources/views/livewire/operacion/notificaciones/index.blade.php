@section('page-title', 'Notificaciones')
@section('page-description', 'Búsqueda y filtros avanzados de notificaciones')

<div class="max-w-5xl mx-auto space-y-5" x-data="{ filtrosAbiertos: true }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center gap-2">
            <button type="button"
                @click="filtrosAbiertos = !filtrosAbiertos"
                class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition shadow-sm">
                <i class="fas fa-sliders-h"
                    :class="{ 'text-blue-500': filtrosAbiertos, 'text-gray-500': !filtrosAbiertos }"></i>
                <span>Filtros</span>
                <span class="text-xs bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded-md font-bold" x-show="!filtrosAbiertos">
                    Avanzados
                </span>
                <i class="fas"
                    :class="{ 'fa-chevron-up': filtrosAbiertos, 'fa-chevron-down': !filtrosAbiertos }"></i>
            </button>
        </div>
    </div>

    <div x-show="filtrosAbiertos" x-collapse x-cloak>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-1">
                    <i class="fas fa-search mr-2 text-blue-500"></i>Buscar Notificaciones
                </h2>
                <p class="text-sm text-gray-500">
                    Completa uno o más filtros y presiona Buscar.
                </p>
            </div>

            <form wire:submit.prevent="buscar" class="space-y-4">
                <div class="space-y-4">

                    {{-- Fila 1: Contribuyente y Barrio --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Contribuyente (nombre o identificación)
                            </label>
                            <input type="text"
                                wire:model.live.debounce.400ms="contribuyente"
                                placeholder="Ej. Juan Pérez o 1712345678"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Barrio Geográfico
                            </label>
                            <select wire:model.live="barrio_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Todos los barrios activos</option>
                                @foreach($barrios as $barrio)
                                <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Fila 2: Contravención --}}
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Artículo de Ordenanza 332
                            </label>
                            <select wire:model.live="ordenanza332_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Todas las contravenciones</option>
                                @foreach($contravenciones as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->codigo }} - {{ Str::limit($item->descripcion ?? 'Sin descripción', 45) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Fila 3: Rango de fecha de notificación --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Desde (Fecha de Notificación)
                            </label>
                            <input type="date"
                                wire:model.live="fecha_inicio"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Hasta (Fecha de Notificación)
                            </label>
                            <input type="date"
                                wire:model.live="fecha_fin"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                        </div>
                    </div>

                    {{-- Fila 4: Estado de revisión y Rol --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Estado de Revisión
                            </label>
                            <select wire:model.live="estado_revision"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Cualquier estado</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Enviada">Enviada</option>
                                <option value="Verificada">Verificada</option>
                                <option value="Aprobada">Aprobada</option>
                                <option value="Rechazada">Rechazada</option>
                                <option value="Vencida">Vencida</option>
                                <option value="Cerrada">Cerrada</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Rol que Revisó
                            </label>
                            <select wire:model.live="rol"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                                <option value="">Cualquier rol</option>
                                @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Fila 5: Rango de fecha de revisión --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Desde (Fecha de Revisión)
                            </label>
                            <input type="date"
                                wire:model.live="fecha_revision_inicio"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Hasta (Fecha de Revisión)
                            </label>
                            <input type="date"
                                wire:model.live="fecha_revision_fin"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex gap-3">
                        <button type="submit"
                            @click="filtrosAbiertos = false"
                            class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                        <button type="button" wire:click="limpiar"
                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                            Limpiar
                        </button>
                    </div>

                    <div wire:loading class="w-full pt-2">
                        <div class="flex items-center justify-center gap-2 text-sm text-blue-600 font-medium animate-pulse">
                            <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sincronizando filtros y datos...
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div>
        @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center shadow-xs">
            <svg class="w-5 h-5 text-green-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p class="text-green-800 font-medium">{{ session('message') }}</p>
        </div>
        @endif

        <div class="flex flex-wrap gap-3 mb-4 items-center justify-between">
            <div class="flex gap-2">
                <a href="{{ route('notificaciones.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm flex items-center gap-1">
                    <i class="fas fa-arrow-left text-xs"></i> Modificar Filtros
                </a>
            </div>

            <div>
                <select wire:model.live="perPage" class="border px-3 py-2 rounded-lg bg-white text-sm focus:ring-2 focus:ring-blue-500 outline-hidden">
                    <option value="5">5 por página</option>
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th wire:click="sortBy('id')" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                            ID @if($sortField === 'id') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contribuyente</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barrio</th>
                        <th wire:click="sortBy('fecha_notificacion')" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                            Fecha @if($sortField === 'fecha_notificacion') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Predio</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ordenanza</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Evidencia</th>
                        <th wire:click="sortBy('fecha_vencimiento')" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                            Vencimiento @if($sortField === 'fecha_vencimiento') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
                        </th>
                        <th wire:click="sortBy('estado')" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                            Estado @if($sortField === 'estado') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Revisión</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Seguridad On-Chain</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($notificaciones as $notificacion)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 text-sm font-mono text-gray-600">#{{ $notificacion->id }}</td>

                        <td class="px-4 py-4 text-sm text-gray-900">
                            <p class="font-medium">{{ $notificacion->contribuyente_nombre ?: '—' }}</p>
                            <span class="text-xs text-gray-400">{{ $notificacion->contribuyente_identificacion }}</span>
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $notificacion->barrio->nombre ?? '—' }}
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $notificacion->fecha_notificacion?->format('d/m/Y H:i') ?? '—' }}
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ $notificacion->numero_predio ?: '—' }}
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-600">
                            @if($notificacion->ordenanza332)
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-semibold rounded-sm" title="{{ $notificacion->ordenanza332->descripcion }}">
                                Art. {{ $notificacion->ordenanza332->codigo }}
                            </span>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-4 text-center">
                            @if($notificacion->evidencia_path)
                            <a href="{{ $notificacion->evidencia_url }}" target="_blank"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100 transition">
                                @if($notificacion->evidencia_tipo === 'foto')
                                <i class="fas fa-camera"></i> Foto
                                @else
                                <i class="fas fa-video"></i> Video
                                @endif
                            </a>
                            @else
                            <span class="text-xs text-gray-400">Sin archivo</span>
                            @endif
                        </td>

                        <td class="px-4 py-4 text-center text-xs text-gray-600 whitespace-nowrap">
                            {{ $notificacion->fecha_vencimiento?->format('d/m/Y H:i') ?? '—' }}
                        </td>

                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                {{ Str::lower($notificacion->estado) === 'pendiente' ? 'bg-gray-100 text-gray-800 border border-gray-200' : '' }}
                                {{ Str::lower($notificacion->estado) === 'enviada' ? 'bg-indigo-100 text-indigo-800 border border-indigo-200' : '' }}
                                {{ Str::lower($notificacion->estado) === 'verificada' ? 'bg-blue-100 text-blue-800 border border-blue-200' : '' }}
                                {{ Str::lower($notificacion->estado) === 'aprobada' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                                {{ Str::lower($notificacion->estado) === 'rechazada' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                                {{ Str::lower($notificacion->estado) === 'vencida' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : '' }}
                                {{ Str::lower($notificacion->estado) === 'cerrada' ? 'bg-slate-200 text-slate-800 border border-slate-300' : '' }}">
                                {{ $notificacion->estado }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-sm text-gray-600">
                            @if($notificacion->revisor && isset($notificacion->revisor['nombre']))
                            <p class="font-medium text-gray-800">
                                {{ $notificacion->revisor['nombre'] }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $notificacion->revisor['rol'] }} · {{ $notificacion->revisor['fecha']?->format('d/m/Y H:i') }}
                            </p>
                            @else
                            <span class="text-xs text-gray-400 italic">Esperando revisión</span>
                            @endif
                        </td>

                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center p-1 rounded-full text-sm
                                {{ $notificacion->verified_on_chain ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}"
                                title="{{ $notificacion->tx_hash ?? 'Transacción no emitida' }}">
                                <i class="fas {{ $notificacion->verified_on_chain ? 'fa-shield-alt' : 'fa-lock-open' }}"></i>
                            </span>
                        </td>

                        <td class="px-4 py-4 text-sm text-center font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('notificaciones.show', $notificacion->id) }}"
                                    class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                    title="Ver Detalles Completos">
                                    <x-heroicon-s-eye class="w-5 h-5" />
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm">No se encontraron notificaciones registradas bajo los filtros provistos.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $notificaciones->links() }}
        </div>
    </div>
</div>