@section('page-title', 'Lista de Notificaciones')
@section('page-description', 'Resultados de la búsqueda y control de estados')

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