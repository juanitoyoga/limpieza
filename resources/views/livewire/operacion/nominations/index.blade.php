@section('page-title', 'Nominaciones')
@section('page-description', 'Gestión y Búsqueda de Nominaciones')

<div class="max-w-7xl mx-auto space-y-5" x-data="{ filtrosAbiertos: false }">

    {{-- Notificaciones --}}
    @if(session()->has('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center shadow-sm">
        <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
        <p class="text-green-800 font-medium text-xs">{{ session('success') }}</p>
    </div>
    @endif

    {{-- BARRA SUPERIOR --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">

        <div class="flex items-center gap-2 w-full sm:w-auto flex-1">


            {{-- Botón filtros --}}
            <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                class="inline-flex items-center gap-2 px-3.5 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition shadow-sm">
                <i class="fas fa-sliders-h" :class="{ 'text-blue-500': filtrosAbiertos, 'text-gray-500': !filtrosAbiertos }"></i>
                <span>Filtros</span>
                <span class="text-xs bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded-md font-bold" x-show="!filtrosAbiertos">Avanzados</span>
                <i class="fas" :class="{ 'fa-chevron-up': filtrosAbiertos, 'fa-chevron-down': !filtrosAbiertos }"></i>
            </button>
        </div>

    </div>

    {{-- SECCIÓN DE FILTROS COLAPSABLE (Únicamente el formulario de criterios) --}}
    <div x-show="filtrosAbiertos"
        x-collapse
        x-cloak>

        {{-- PANEL DE FILTROS AVANZADOS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

            <div class="mb-4 pb-2 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">Criterios Avanzados</h3>
            </div>

            <form wire:submit.prevent="buscar" class="space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                    {{-- Número de trámite --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                            Número de Trámite
                        </label>
                        <input type="text" wire:model.live="search"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700"
                            placeholder="Ej: FIN-DMQ-2025-0001">
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Estado</label>
                        <select wire:model.live="estado"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Todos</option>
                            <option value="propuesta">Propuesta</option>
                            <option value="verificada">Verificada</option>
                            <option value="aprobada">Aprobada</option>
                            <option value="rechazada">Rechazada</option>
                            <option value="expirada">Expirada</option>
                            <option value="anulada">Anulada</option>
                        </select>
                    </div>

                    {{-- Unidad que emite --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                            Unidad que Emite
                        </label>
                        <select wire:model.live="issuer_type"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Todas</option>
                            <option value="DMQ">DMQ</option>
                            <option value="JUNTA_PARROQUIAL">Junta Parroquial</option>
                            <option value="GENERAL">General</option>
                        </select>
                    </div>

                    {{-- Rol --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Rol</label>
                        <input type="text" wire:model.live="rol"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700"
                            placeholder="Ej: Supervisor">
                    </div>

                    {{-- Nominador --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nominador</label>
                        <select wire:model.live="nominator_id"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Todos</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Candidato --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Candidato</label>
                        <select wire:model.live="candidate_user_id"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Todos</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Verificado por --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Verificado por</label>
                        <select wire:model.live="verified_by"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Todos</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rechazado por --}}
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Rechazado por</label>
                        <select wire:model.live="rejected_by"
                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                            <option value="">Todos</option>
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Fechas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Fecha emisión --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Emisión Desde</label>
                        <input type="date" wire:model.live="fecha_emision_inicio"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Emisión Hasta</label>
                        <input type="date" wire:model.live="fecha_emision_fin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    {{-- Vigencia --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vigencia Desde</label>
                        <input type="date" wire:model.live="vigencia_inicio"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vigencia Hasta</label>
                        <input type="date" wire:model.live="vigencia_fin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    {{-- Verificado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Verificado Desde</label>
                        <input type="date" wire:model.live="verified_at_inicio"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Verificado Hasta</label>
                        <input type="date" wire:model.live="verified_at_fin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    {{-- Rechazado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rechazado Desde</label>
                        <input type="date" wire:model.live="rejected_at_inicio"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rechazado Hasta</label>
                        <input type="date" wire:model.live="rejected_at_fin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                    <button type="button" wire:click="limpiar"
                        class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                        Restablecer
                    </button>

                    {{-- Se añadió @click para colapsar los filtros al aplicar --}}
                    <button type="submit" @click="filtrosAbiertos = false"
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        Aplicar Filtros
                    </button>
                </div>

            </form>
        </div>

    </div> {{-- FIN DEL CONTAINER COLAPSABLE DE FILTROS --}}


    {{-- TABLA DE RESULTADOS (Ahora está fuera del colapso, siempre visible) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-gray-100/70 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('numero_tramite')">N° Trámite</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Nominador</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Candidato</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Rol</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Unidad Emite</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('fecha_emision')">Fecha Emisión</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Vigencia</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Estado</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Verificado por</th>

                        <th class="px-5 py-3 font-bold text-gray-600 uppercase tracking-wider">Rechazado por</th>

                        <th class="px-5 py-3 text-center font-bold text-gray-600 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 text-gray-600">

                    @forelse($nominations as $n)
                    <tr class="hover:bg-blue-50/20 transition">

                        {{-- Número trámite --}}
                        <td class="px-5 py-3.5 font-semibold text-gray-900">
                            {{ $n->numero_tramite }}
                        </td>

                        {{-- Nominador --}}
                        <td class="px-5 py-3.5">
                            {{ $n->nominator?->first_name }} {{ $n->nominator?->last_name }}
                        </td>

                        {{-- Candidato --}}
                        <td class="px-5 py-3.5">
                            {{ $n->candidate?->first_name }} {{ $n->candidate?->last_name }}
                        </td>

                        {{-- Rol --}}
                        <td class="px-5 py-3.5">
                            {{ $n->role_name }}
                        </td>

                        {{-- Unidad que emite --}}
                        <td class="px-5 py-3.5">
                            {{ $n->issuer_type }}
                        </td>

                        {{-- Fecha emisión --}}
                        <td class="px-5 py-3.5 whitespace-nowrap text-gray-500">
                            {{ $n->fecha_emision?->format('d/m/Y') }}
                        </td>

                        {{-- Vigencia --}}
                        <td class="px-5 py-3.5 whitespace-nowrap text-gray-500">
                            {{ $n->fecha_inicio_vigencia?->format('d/m/Y') }} –
                            {{ $n->fecha_fin_vigencia?->format('d/m/Y') }}
                        </td>

                        {{-- Estado --}}
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium text-white shadow-xs {{ $n->estadoColor() }}">
                                {{ $n->estadoLabel() }}
                            </span>
                        </td>

                        {{-- Verificado por --}}
                        <td class="px-5 py-3.5">
                            @if($n->verified_by)
                            {{ $n->verifier?->first_name }} {{ $n->verifier?->last_name }}
                            <br>
                            <span class="text-[10px] text-gray-500">
                                {{ $n->verified_at?->format('d/m/Y H:i') }}
                            </span>
                            @else
                            —
                            @endif
                        </td>

                        {{-- Rechazado por --}}
                        <td class="px-5 py-3.5">
                            @if($n->rejected_by)
                            {{ $n->rejectedBy?->first_name }} {{ $n->rejectedBy?->last_name }}
                            <br>
                            <span class="text-[10px] text-gray-500">
                                {{ $n->rejected_at?->format('d/m/Y H:i') }}
                            </span>
                            @else
                            —
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-5 py-3.5 text-center">
                            <div class="inline-flex gap-1">

                                {{-- Ver --}}
                                <a href="{{ route('nominations.show', $n->id) }}"
                                    class="p-1.5 rounded text-gray-400 hover:text-blue-600 hover:bg-gray-100 transition"
                                    title="Ver Detalle">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                {{-- Anular --}}
                                @if($n->estado !== 'anulada')
                                <button type="button" wire:click="confirmAnular({{ $n->id }})"
                                    class="p-1.5 rounded text-gray-400 hover:text-red-600 hover:bg-gray-100 transition"
                                    title="Anular Trámite">
                                    <i class="fas fa-ban text-sm"></i>
                                </button>
                                @else
                                <span class="p-1.5 text-gray-300 cursor-not-allowed" title="Ya anulado">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                @endif

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-5 py-12 text-center text-gray-400 bg-gray-50/50">
                            <p class="text-xs font-medium">No se encontraron nominaciones.</p>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        @if(!$nominations->isEmpty())
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/70">
            {{ $nominations->links() }}
        </div>
        @endif

    </div>

</div> {{-- Cierre final de x-data --}}