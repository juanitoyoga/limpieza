@section('page-title', 'Registro de Vecino')
@section('page-description', 'Completa la información para registrarte como vecino')

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <h2 class="text-xl font-semibold text-gray-800 mb-6">
            <i class="fas fa-user-plus text-blue-500 mr-2"></i>
            Registro de Vecino
        </h2>

        {{-- Formulario --}}
        <form wire:submit.prevent="save" class="space-y-6">

            {{-- Barrio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio (GeoPis)</label>
                <select wire:model="id_DMQ"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Seleccione un barrio</option>
                    @foreach($barrios as $barrio)
                    <option value="{{ $barrio->id_DMQ }}">
                        {{ $barrio->nombre }} ({{ $barrio->id_DMQ }})
                    </option>
                    @endforeach
                </select>
                @error('id_DMQ') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Dirección --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Calle Principal</label>
                    <input type="text" wire:model="calle_principal"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                    @error('calle_principal') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" wire:model="numero"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                    @error('numero') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Calle Secundaria</label>
                <input type="text" wire:model="calle_secundaria"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                @error('calle_secundaria') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" wire:model="telefono"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition">
                @error('telefono') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Referencias --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Referencias</label>
                <textarea wire:model="referencias" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition"></textarea>
                @error('referencias') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Ocupación --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ocupación</label>
                <select wire:model="ocupaciones" multiple
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition h-32">
                    @foreach($catalogoOcupaciones as $item)
                    <option value="{{ $item->nombre }}">{{ $item->nombre }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Puedes seleccionar varias.</p>
            </div>

            {{-- Deportes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deportes</label>
                <select wire:model="deportes" multiple
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition h-32">
                    @foreach($catalogoDeportes as $item)
                    <option value="{{ $item->nombre }}">{{ $item->nombre }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Puedes seleccionar varios.</p>
            </div>

            {{-- Recreación / Hobbies --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recreación / Hobbies</label>
                <select wire:model="recreaciones" multiple
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition h-32">
                    @foreach($catalogoRecreaciones as $item)
                    <option value="{{ $item->nombre }}">{{ $item->nombre }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Puedes seleccionar varios.</p>
            </div>


            {{-- Botones --}}
            <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                <a href="{{ route('vecinos.index') }}"
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                    Cancelar
                </a>

                <button type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    Registrar Vecino
                </button>
            </div>

        </form>
    </div>
</div>