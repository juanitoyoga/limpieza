@section('page-title', 'Denuncias')
@section('page-description', 'Búsqueda y filtros avanzados de infracciones')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">
                <i class="fas fa-search mr-2 text-blue-500"></i>Buscar Denuncias
            </h2>
            <p class="text-sm text-gray-500">
                Completa uno o más filtros y presiona Buscar.
            </p>
        </div>

        <form wire:submit.prevent="buscar" class="space-y-4">


            <div class="space-y-4">

                {{-- Fila 1: Vecino y Barrio --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Vecino
                        </label>
                        <select wire:model.live="vecino_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Todos los vecinos</option>
                            @foreach($vecinos as $vecino)
                            <option value="{{ $vecino->id }}">{{ $vecino->user->first_name }} {{ $vecino->user->last_name }}</option>
                            @endforeach
                        </select>
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

                {{-- Fila 3: Rango de fecha de denuncia --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Desde (Fecha de Denuncia)
                        </label>
                        <input type="date"
                            wire:model.live="fecha_inicio"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Hasta (Fecha de Denuncia)
                        </label>
                        <input type="date"
                            wire:model.live="fecha_fin"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition text-gray-700" />
                    </div>
                </div>

                {{-- Fila 4: Estado de revisión (verificado/aprobado/rechazado) y Rol --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estado de Revisión
                        </label>
                        <select wire:model.live="estado_revision"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">Cualquier estado</option>
                            <option value="verificado">Verificado</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="rechazado">Rechazado</option>
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
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        <i class="fas fa-search mr-2"></i>Buscar
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