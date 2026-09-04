@section('page-title', 'Lista de Denuncias')
@section('page-description', 'Resultados de la búsqueda y control de estados')

<div>
    {{-- Mensajes de Notificación --}}
    @if(session()->has('message'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center shadow-xs">
        <svg class="w-5 h-5 text-green-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <p class="text-green-800 font-medium">{{ session('message') }}</p>
    </div>
    @endif

    {{-- Barra Superior de Control --}}
    <div class="flex flex-wrap gap-3 mb-4 items-center justify-between">
        <div class="flex gap-2">
            <a href="{{ route('denuncias.index') }}"
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

    {{-- Tabla de Datos Reactiva --}}
    <div class="overflow-x-auto bg-white rounded-xl shadow border border-gray-200">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th wire:click="sortBy('id')" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                        ID @if($sortField === 'id') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Vecino</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barrio</th>
                    <th wire:click="sortBy('fecha_denuncia')" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                        Fecha @if($sortField === 'fecha_denuncia') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
                    </th>
                    <th wire:click="sortBy('direccion')" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                        Dirección @if($sortField === 'direccion') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Orden Sancionatoria</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Evidencia</th>
                    <th wire:click="sortBy('multa_calculada')" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase cursor-pointer hover:bg-gray-100 transition">
                        Multa ($) @if($sortField === 'multa_calculada') {!! $sortDirection === 'asc' ? '▲' : '▼' !!} @endif
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
                @forelse($denuncias as $denuncia)
                <tr class="hover:bg-gray-50 transition {{ !$denuncia->activo ? 'bg-gray-50/50 italic text-gray-400' : '' }}">
                    {{-- ID --}}
                    <td class="px-4 py-4 text-sm font-mono text-gray-600">#{{ $denuncia->id }}</td>

                    {{-- Vecino --}}
                    <td class="px-4 py-4 text-sm text-gray-900">
                        @if($denuncia->vecino && $denuncia->vecino->user)
                        {{ $denuncia->vecino->user->first_name }} {{ $denuncia->vecino->user->last_name }}
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>

                    {{-- Barrio --}}
                    <td class="px-4 py-4 text-sm text-gray-700">
                        {{ $denuncia->barrio->nombre ?? '—' }}
                    </td>

                    {{-- Fecha de la denuncia --}}
                    <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                        {{ $denuncia->fecha_denuncia?->format('d/m/Y H:i') ?? '—' }}
                    </td>

                    {{-- Dirección --}}
                    <td class="px-4 py-4 text-sm">
                        <p class="font-medium text-gray-900 max-w-xs truncate" title="{{ $denuncia->direccion }}">{{ $denuncia->direccion ?: 'Sin dirección registrada' }}</p>
                        @if($denuncia->direccion_gps)
                        <span class="text-xs text-gray-400 block max-w-xs truncate" title="{{ $denuncia->direccion_gps }}"><i class="fas fa-map-marker-alt text-red-400 mr-1"></i>{{ $denuncia->direccion_gps }}</span>
                        @endif
                    </td>

                    {{-- Ordenanza --}}
                    <td class="px-4 py-4 text-sm text-gray-600">
                        @if($denuncia->ordenanza332)
                        <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-semibold rounded-sm" title="{{ $denuncia->ordenanza332->descripcion }}">
                            Art. {{ $denuncia->ordenanza332->codigo }}
                        </span>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>

                    {{-- Evidencia multimedial --}}
                    <td class="px-4 py-4 text-center">
                        @if($denuncia->evidencia_path)
                        <a href="{{ $denuncia->evidencia_url }}" target="_blank"
                            class="inline-flex items-center gap-1 px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100 transition">
                            @if($denuncia->evidencia_tipo === 'foto')
                            <i class="fas fa-camera"></i> Foto
                            @else
                            <i class="fas fa-video"></i> Video
                            @endif
                        </a>
                        @else
                        <span class="text-xs text-gray-400">Sin archivo</span>
                        @endif
                    </td>

                    {{-- Multa calculada --}}
                    <td class="px-4 py-4 text-center text-sm font-semibold text-gray-900">
                        ${{ number_format($denuncia->multa_calculada, 2) }}
                    </td>

                    {{-- Estado del flujo --}}
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            {{ Str::lower($denuncia->estado) === 'pendiente' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : '' }}
                            {{ Str::lower($denuncia->estado) === 'verificado' ? 'bg-blue-100 text-blue-800 border border-blue-200' : '' }}
                            {{ Str::lower($denuncia->estado) === 'aprobado' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                            {{ Str::lower($denuncia->estado) === 'rechazado' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}">
                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full 
                                {{ Str::lower($denuncia->estado) === 'pendiente' ? 'bg-yellow-500' : '' }}
                                {{ Str::lower($denuncia->estado) === 'verificado' ? 'bg-blue-500' : '' }}
                                {{ Str::lower($denuncia->estado) === 'aprobado' ? 'bg-green-500' : '' }}
                                {{ Str::lower($denuncia->estado) === 'rechazado' ? 'bg-red-500' : '' }}">
                            </span>
                            {{ ucfirst($denuncia->estado) }}
                        </span>
                    </td>

                    {{-- Revisión: nombre, rol y fecha según estado --}}
                    <td class="px-4 py-4 text-sm text-gray-600">
                        @if($denuncia->revisor && isset($denuncia->revisor['nombre']))
                        <p class="font-medium text-gray-800">
                            {{ $denuncia->revisor['nombre'] }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $denuncia->revisor['rol'] }} · {{ $denuncia->revisor['fecha']?->format('d/m/Y H:i') }}
                        </p>
                        @else
                        <span class="text-xs text-gray-400 italic">Esperando revisión</span>
                        @endif
                    </td>

                    {{-- Validación Blockchain --}}
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center justify-center p-1 rounded-full text-sm
                            {{ $denuncia->verified_on_chain ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}"
                            title="{{ $denuncia->tx_hash ?? 'Transacción no emitida' }}">
                            <i class="fas {{ $denuncia->verified_on_chain ? 'fa-shield-alt' : 'fa-lock-open' }}"></i>
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-center font-medium">
                        <div class="flex items-center justify-center gap-2">

                            {{-- Acciones Universales --}}
                            <a href="{{ route('denuncias.show', $denuncia->id) }}"
                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                title="Ver Detalles Completos">
                                <x-heroicon-s-eye class="w-5 h-5" />
                            </a>

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="12" class="px-6 py-12 text-center text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p class="text-sm">No se encontraron denuncias registradas bajo los filtros provistos.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Vínculos de Paginación Pura de Livewire --}}
    <div class="mt-4">
        {{ $denuncias->links() }}
    </div>


</div>