@section('page-title', 'Editar Resolución')
@section('page-description', 'Registro de resoluciones')

<div class="bg-white p-6 rounded-xl shadow border border-gray-200 max-w-3xl mx-auto">

    <h2 class="text-xl font-bold mb-6">Editar Resolución</h2>

    <form wire:submit.prevent="save" class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                <input type="text" wire:model="codigo" class="w-full border px-4 py-2 rounded">
                @error('codigo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio ID</label>
                <input type="number" wire:model="barrio_id" class="w-full border px-4 py-2 rounded">
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
                <input type="text" wire:model="tipo" class="w-full border px-4 py-2 rounded">
                @error('tipo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Resolución</label>
                <input type="date" wire:model="fecha_resolucion" class="w-full border px-4 py-2 rounded">
                @error('fecha_resolucion') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento Path</label>
                <input type="text" wire:model="documento_original_path" class="w-full border px-4 py-2 rounded">
                @error('documento_original_path') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento Hash</label>
                <input type="text" wire:model="documento_original_hash" class="w-full border px-4 py-2 rounded">
                @error('documento_original_hash') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento MIME</label>
                <input type="text" wire:model="documento_original_mime" class="w-full border px-4 py-2 rounded">
                @error('documento_original_mime') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Firmas</label>
                <input type="number" wire:model="numero_firmas" class="w-full border px-4 py-2 rounded">
                @error('numero_firmas') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado Autenticación</label>
                <input type="text" wire:model="auth_status" class="w-full border px-4 py-2 rounded">
                @error('auth_status') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('resoluciones.lista') }}"
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