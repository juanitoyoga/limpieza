@section('page-title', 'Nueva Resolución')
@section('page-description', 'Registro de resoluciones')

<div class="bg-white p-6 rounded-xl shadow border border-gray-200 max-w-3xl mx-auto">

    <h2 class="text-xl font-bold mb-6">Crear Resolución</h2>

    @error('global')
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        {{ $message }}
    </div>
    @enderror

    <form wire:submit.prevent="save" class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                <input type="text" wire:model="codigo" class="w-full border px-4 py-2 rounded">
                @error('codigo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                <select wire:model="barrio_id" class="w-full border px-4 py-2 rounded">
                    <option value="">Seleccione...</option>
                    @foreach($barrios as $barrio)
                    <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                    @endforeach
                </select>
                @error('barrio_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input type="text" wire:model="titulo" class="w-full border px-4 py-2 rounded">
            @error('titulo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea wire:model="descripcion" rows="3" class="w-full border px-4 py-2 rounded"></textarea>
            @error('descripcion') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select wire:model="service_type_id" class="w-full border px-4 py-2 rounded">
                    <option value="">Seleccione...</option>
                    @foreach($service_types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                @error('service_type_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Resolución</label>
                <input type="date" wire:model="fecha_resolucion" class="w-full border px-4 py-2 rounded">
                @error('fecha_resolucion') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Carga del Documento PDF --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Documento PDF (Máx. 5MB)</label>
            <input type="file" wire:model="documento_pdf" accept="application/pdf" class="w-full border px-4 py-2 rounded bg-gray-50">
            <div wire:loading wire:target="documento_pdf" class="text-sm text-blue-600 mt-1">
                Subiendo documento...
            </div>
            @error('documento_pdf') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Firmas</label>
                <input type="number" wire:model="numero_firmas" class="w-full border px-4 py-2 rounded">
                @error('numero_firmas') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Servicios</label>
                <input type="number" wire:model="numero_servicios" class="w-full border px-4 py-2 rounded">
                @error('numero_servicios') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('resoluciones.lista') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition disabled:opacity-50">
                Guardar
            </button>
        </div>
    </form>
</div>