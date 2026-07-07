@section('page-title', 'Contratos')
@section('page-description', 'Gestión y Búsqueda de Contratos')

<div class="max-w-7xl mx-auto space-y-5" x-data="{ filtrosAbiertos: false }">

    {{-- Notificaciones de Éxito o Error --}}
    @if(session()->has('message'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center shadow-sm">
        <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
        <p class="text-green-800 font-medium text-xs">{{ session('message') }}</p>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center shadow-sm">
        <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
        <p class="text-red-800 font-medium text-xs">{{ session('error') }}</p>
    </div>
    @endif

    {{-- BARRA SUPERIOR DE ACCIONES RÁPIDAS --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-2 w-full sm:w-auto flex-1">
            <div class="relative w-full max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" wire:model.live="numero_contrato" placeholder="Buscar por número..."
                    class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 text-gray-700 transition" />
            </div>

            <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition shadow-sm">
                <i class="fas fa-sliders-h text-gray-500" :class="filtrosAbiertos ? 'text-blue-500' : ''"></i>
                <span>Filtros</span>
                <span class="text-xs bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded-md font-bold" x-show="!filtrosAbiertos">Avanzados</span>
                <i class="fas" :class="filtrosAbiertos ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <a href="{{ route('contratos.create') }}"
                class="inline-flex items-center bg-blue-600 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition">
                <i class="fas fa-plus mr-2"></i>Nuevo Contrato
            </a>
        </div>
    </div>

    {{-- PANEL DESPLEGABLE DE FILTROS AVANZADOS (Se mantiene compacto) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-show="filtrosAbiertos" x-collapse style="display: none;">
        <div class="mb-4 pb-2 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-800">Criterios de Segmentación Avanzada</h3>
        </div>

        <form wire:submit.prevent="buscar" class="space-y-4">
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    {{-- [Tus inputs de filtros: Barrio, Estado, Blockchain, Montos, Fechas, Roles...] --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Barrio</label>
                        <select wire:model.live="barrio_id" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Todos los barrios</option>
                            @foreach($barrios as $barrio) <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option> @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Estado</label>
                        <select wire:model.live="estado" class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Cualquier estado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="verificado">Verificado</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
                            </option>
                        </select>
                    </div>
                </div>


                {{-- Fila 4: Rango de fecha de inicio y fin del contrato --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Vigencia Desde
                        </label>
                        <input type="date"
                            wire:model.live="fecha_inicio"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Vigencia Hasta
                        </label>
                        <input type="date"
                            wire:model.live="fecha_fin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>
                </div>

                {{-- Fila 5: Rol que ingresó y Rol que verificó --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rol que Ingresó
                        </label>
                        <select wire:model.live="rol_ingreso"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Cualquier rol</option>
                            @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rol que Verificó
                        </label>
                        <select wire:model.live="rol_verificacion"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Cualquier rol</option>
                            @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Fila 6: Rol que aprobó y presencia de registro blockchain --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rol que Aprobó
                        </label>
                        <select wire:model.live="rol_aprobacion"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Cualquier rol</option>
                            @foreach($roles as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Registro Blockchain
                        </label>
                        <select wire:model.live="con_blockchain"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Todos</option>
                            <option value="1">Con registro blockchain</option>
                            <option value="0">Sin registro blockchain</option>
                        </select>
                    </div>
                </div>

                {{-- Fila 7: Rango de fecha de ingreso --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Desde (Fecha de Ingreso)
                        </label>
                        <input type="date"
                            wire:model.live="fecha_ingreso_inicio"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Hasta (Fecha de Ingreso)
                        </label>
                        <input type="date"
                            wire:model.live="fecha_ingreso_fin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>
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

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" wire:click="limpiar" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">Restablecer</button>
                <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">Aplicar Filtros</button>
            </div>
        </form>
    </div>

    {{-- TABLA DE RESULTADOS REESTRUCTURADA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-gray-100/70 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('numero_contrato')">Número Contrato</th>
                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('barrio_id')">Barrio</th>
                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('estado')">Estado</th>
                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider cursor-pointer" wire:click="sortBy('fecha_inicio')">Vigencia</th>
                        {{-- Cabecera unificada de métricas porcentuales --}}
                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Porcentajes (Barrio / DMQ / LTR)</th>
                        <th class="px-5 py-3 text-center font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($contratos as $contrato)
                    <tr class="hover:bg-blue-50/20 transition">

                        {{-- 1. Número Contrato --}}
                        <td class="px-5 py-3.5 font-semibold text-gray-900">
                            {{ $contrato->numero_contrato ?? '—' }}
                        </td>

                        {{-- 2. Barrio --}}
                        <td class="px-5 py-3.5 text-gray-700">
                            {{ $contrato->barrio->nombre ?? '—' }}
                        </td>

                        {{-- 3. Estado --}}
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium text-white shadow-xs {{ $contrato->estadoColor() }}">
                                {{ $contrato->estadoLabel() }}
                            </span>
                        </td>

                        {{-- 4. Vigencia --}}
                        <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">
                            {{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }} al {{ $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}
                        </td>

                        {{-- 5. Porcentajes Solicitados --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center bg-slate-100 text-slate-700 px-2 py-1 rounded text-[11px] font-medium border border-slate-200">
                                    <strong class="mr-1 text-slate-500">B:</strong> {{ $contrato->porcentaje_barrio ?? 0 }}%
                                </span>
                                <span class="inline-flex items-center bg-blue-50 text-blue-700 px-2 py-1 rounded text-[11px] font-medium border border-blue-100">
                                    <strong class="mr-1 text-blue-400">DMQ:</strong> {{ $contrato->porcentaje_dmq ?? 0 }}%
                                </span>
                                <span class="inline-flex items-center bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-[11px] font-medium border border-indigo-100">
                                    <strong class="mr-1 text-indigo-400">LTR:</strong> {{ $contrato->porcentaje_ltr ?? 0 }}%
                                </span>
                            </div>
                        </td>

                        {{-- 6. Acciones (Condición estricta de edición) --}}
                        <td class="px-5 py-3.5 text-center">
                            <div class="inline-flex gap-1">
                                <a href="{{ route('contratos.show', $contrato->id) }}" class="p-1.5 rounded text-gray-400 hover:text-blue-600 hover:bg-gray-100 transition" title="Ver Detalle">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                @if($contrato->estado === 'pendiente')
                                {{-- El botón llama al método seguro del componente para validar el estado --}}
                                <button type="button" wire:click="editarContrato({{ $contrato->id }})" class="p-1.5 rounded text-gray-400 hover:text-amber-600 hover:bg-gray-100 transition" title="Editar Registro">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                @else
                                {{-- Candado visual informativo si no está pendiente --}}
                                <span class="p-1.5 text-gray-300 cursor-not-allowed" title="Edición deshabilitada (Solo contratos pendientes)">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400 bg-gray-50/50">
                            <p class="text-xs font-medium">No se encontraron registros de contratos.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!$contratos->isEmpty())
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/70">
            {{ $contratos->links() }}
        </div>
        @endif
    </div>
</div>