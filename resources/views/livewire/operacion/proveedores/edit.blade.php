@section('page-title', 'Editar Proveedor')
@section('page-description', 'Registro de proveedores')

<div class="bg-white p-6 rounded-xl shadow border border-gray-200 max-w-3xl mx-auto">

    <h2 class="text-xl font-bold mb-6">Editar Proveedor</h2>

    <form wire:submit.prevent="save" class="space-y-6">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social</label>
            <input type="text" wire:model="razon_social" class="w-full border px-4 py-2 rounded">
            @error('razon_social') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                <input type="text" wire:model="ruc" maxlength="13" class="w-full border px-4 py-2 rounded">
                @error('ruc') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Representante Legal</label>
                <input type="text" wire:model="representante_legal" class="w-full border px-4 py-2 rounded">
                @error('representante_legal') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Servicio</label>
            <input type="text" wire:model="tipo_servicio" class="w-full border px-4 py-2 rounded"
                placeholder="Ej. Obra civil, consultoría, materiales...">
            @error('tipo_servicio') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full border px-4 py-2 rounded">
                @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" wire:model="telefono" class="w-full border px-4 py-2 rounded">
                @error('telefono') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
            <input type="text" wire:model="direccion" class="w-full border px-4 py-2 rounded">
            @error('direccion') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta Bancaria</label>
                <input type="text" wire:model="cuenta_bancaria" class="w-full border px-4 py-2 rounded">
                @error('cuenta_bancaria') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
                <input type="text" wire:model="banco" class="w-full border px-4 py-2 rounded">
                @error('banco') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select wire:model="estado" class="w-full border px-4 py-2 rounded">
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
            @error('estado') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('proveedores.lista') }}"
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