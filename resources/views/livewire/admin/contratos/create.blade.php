@section('page-title', 'Nuevo Contrato')
@section('page-description', 'Registro de Contratos')

<div class="bg-white p-6 rounded-xl shadow border border-gray-200 max-w-3xl mx-auto">

    <h2 class="text-xl font-bold mb-6">Crear Contrato</h2>

    <form wire:submit.prevent="save" class="space-y-6">

        {{-- Barrio --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
            <select wire:model="barrio_id"
                class="w-full border px-4 py-2 rounded">
                <option value="">Seleccione...</option>
                @foreach($barrios as $barrio)
                <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                @endforeach
            </select>
            @error('barrio_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Número de contrato --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Contrato</label>
            <input type="text" wire:model="numero_contrato"
                class="w-full border px-4 py-2 rounded">
            @error('numero_contrato') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Fechas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" wire:model="fecha_inicio"
                    class="w-full border px-4 py-2 rounded">
                @error('fecha_inicio') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" wire:model="fecha_fin"
                    class="w-full border px-4 py-2 rounded">
                @error('fecha_fin') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Monto --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Monto Total</label>
            <input type="number" step="0.01" wire:model="monto_total"
                class="w-full border px-4 py-2 rounded">
            @error('monto_total') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Porcentajes --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">% Barrio</label>
                <input type="number" wire:model="porcentaje_barrio"
                    class="w-full border px-4 py-2 rounded">
                @error('porcentaje_barrio') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">% DMQ</label>
                <input type="number" wire:model="porcentaje_dmq"
                    class="w-full border px-4 py-2 rounded">
                @error('porcentaje_dmq') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">% LTR</label>
                <input type="number" wire:model="porcentaje_ltr"
                    class="w-full border px-4 py-2 rounded">
                @error('porcentaje_ltr') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Archivo --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Archivo PDF (opcional)</label>
            <input type="file" wire:model="archivo"
                class="w-full border px-4 py-2 rounded">
            @error('archivo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

            @if($archivo)
            <p class="text-sm text-gray-600 mt-2">
                Archivo cargado: <strong>{{ $archivo->getClientOriginalName() }}</strong>
            </p>
            @endif
        </div>

        {{-- Botones --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('contratos.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                Cancelar
            </a>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Guardar
            </button>
        </div>

    </form>
</div>