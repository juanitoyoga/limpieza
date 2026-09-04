@section('page-title', 'Ofertas de Servicios')
@section('page-description', 'Gestión de ofertas de servicios asociadas a resoluciones')

<div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Crear Oferta de Servicios</h1>
    </div>

    @error('global')
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
        {{ $message }}
    </div>
    @enderror

    <form wire:submit="save" class="bg-white p-6 rounded-xl shadow border border-gray-200 space-y-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
            <input type="text" wire:model="codigo" class="w-full border px-4 py-2 rounded">
            @error('codigo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
            <select wire:model="proveedor_id" class="w-full border px-4 py-2 rounded">
                <option value="">-- Seleccione un proveedor --</option>
                @foreach ($proveedores as $proveedor)
                <option value="{{ $proveedor->id }}">{{ $proveedor->razon_social }}</option>
                @endforeach
            </select>
            @error('proveedor_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Resolución</label>
            <select wire:model="resolucion_id" class="w-full border px-4 py-2 rounded">
                <option value="">-- Seleccione una resolución --</option>
                @forelse ($resoluciones as $resolucion)
                <option value="{{ $resolucion->id }}">{{ $resolucion->codigo }} — {{ $resolucion->titulo }}</option>
                @empty
                <option value="" disabled>No hay resoluciones disponibles para tu barrio</option>
                @endforelse
            </select>
            @error('resolucion_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input type="text" wire:model="titulo" class="w-full border px-4 py-2 rounded">
            @error('titulo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
            <textarea wire:model="descripcion" class="w-full border px-4 py-2 rounded" rows="3"></textarea>
            @error('descripcion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Presentación</label>
            <input type="date" wire:model="fecha_presentacion" class="w-full border px-4 py-2 rounded">
            @error('fecha_presentacion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Documento PDF (original firmado)</label>
            <input type="file" wire:model="documento_pdf" accept="application/pdf" class="w-full border px-4 py-2 rounded">

            <div wire:loading wire:target="documento_pdf" class="text-sm text-gray-500 mt-1">
                Subiendo archivo...
            </div>

            @if ($documento_pdf)
            <p class="text-sm text-gray-500 mt-1">Archivo seleccionado: {{ $documento_pdf->getClientOriginalName() }}</p>
            @endif

            @error('documento_pdf') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('ofertas.lista') }}" class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Guardar Oferta</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
        </div>
    </form>
</div>