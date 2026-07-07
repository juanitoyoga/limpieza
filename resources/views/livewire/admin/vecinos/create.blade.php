@section('page-title', 'Registro de Vecino')
@section('page-description', 'Completa la información para registrarte como vecino')

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <h2 class="text-xl font-semibold text-gray-800 mb-6">
            <i class="fas fa-map-marked-alt text-blue-500 mr-2"></i>
            Paso 2: Ubicación Territorial de Vecino
        </h2>

        {{-- Formulario --}}
        <form wire:submit.prevent="save" class="space-y-6">
            @error('server')
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                {{ $message }}
            </div>
            @enderror

            {{-- Barrio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio al que perteneces</label>
                <select wire:model="barrio_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Seleccione un barrio</option>
                    @foreach($barrios as $barrio)
                    <option value="{{ $barrio->id }}">
                        {{ $barrio->nombre }} (Código: {{ $barrio->id_DMQ }})
                    </option>
                    @endforeach
                </select>
                @error('barrio_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- MAPA INTERACTIVO (Google Maps) --}}
            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-street-view text-blue-500 mr-1"></i>
                    Arrastra el marcador exactamente sobre el techo de tu casa
                </label>

                {{-- Contenedor del Mapa --}}
                <div id="map-registration" class="w-full h-64 rounded-lg border border-gray-300 shadow-inner z-10" wire:ignore></div>

                {{-- Inputs ocultos sincronizados con Livewire por el JS --}}
                <input type="hidden" id="input-lat" wire:model="latitud">
                <input type="hidden" id="input-lng" wire:model="longitud">

                {{-- Barra de estado del geocoding --}}
                <div id="polygon-info" class="text-xs text-gray-500 mt-2"></div>

                <div class="grid grid-cols-2 gap-4 mt-2 text-xs text-gray-500">
                    <div>Latitud: <span id="lat-display">53.484251</span></div>
                    <div>Longitud: <span id="lng-display">-2.228189</span></div>
                </div>
                @error('latitud') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                @error('longitud') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Dirección --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calle Principal</label>
                    <input type="text" id="input-calle-principal" wire:model="calle_principal"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                    @error('calle_principal') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de Casa / Lote</label>
                    <input type="text" wire:model="numero"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                    @error('numero') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Calle Secundaria</label>
                <input type="text" id="input-calle-secundaria" wire:model="calle_secundaria"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                @error('calle_secundaria') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono de contacto (Opcional)</label>
                <input type="text" wire:model="telefono" placeholder="Ej: 0999999999"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Referencias --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Referencias de Vivienda</label>
                <textarea wire:model="referencias" rows="2" placeholder="Ej: Frente a la tienda de Don Pepe, casa esquinera color verde."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition"></textarea>
                @error('referencias') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <hr>

            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                <h4 class="text-sm font-semibold text-blue-900 mb-3">Datos Comunitarios (Ayúdanos a conocer el perfil del barrio)</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Ocupación --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tu Ocupación</label>
                        <select wire:model="ocupaciones" multiple
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white h-28 focus:ring-2 focus:ring-blue-500 transition">
                            @foreach($catalogoOcupaciones as $item)
                            <option value="{{ $item->nombre }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Deportes --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Deportes que practicas</label>
                        <select wire:model="deportes" multiple
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white h-28 focus:ring-2 focus:ring-blue-500 transition">
                            @foreach($catalogoDeportes as $item)
                            <option value="{{ $item->nombre }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Recreación / Hobbies --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Hobbies / Recreación</label>
                        <select wire:model="recreaciones" multiple
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white h-28 focus:ring-2 focus:ring-blue-500 transition">
                            @foreach($catalogoRecreaciones as $item)
                            <option value="{{ $item->nombre }}">{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <p class="text-[11px] text-blue-700 mt-2"><i class="fas fa-info-circle mr-1"></i> Mantén presionada la tecla Ctrl (o Cmd en Mac) para elegir múltiples opciones.</p>
            </div>

            {{-- Botones --}}
            <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    Cancelar
                </a>

                <button type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-md hover:shadow-lg">
                    Validar Ubicación y Registrarme
                </button>
            </div>
        </form>
    </div>
</div>



@scripts
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}"></script>
<script src="{{ asset('js/mapas/vecino-create.js') }}"></script>
@endscripts