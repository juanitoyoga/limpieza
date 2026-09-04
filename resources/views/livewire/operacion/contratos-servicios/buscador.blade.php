@section('page-title', 'Generar Contrato de Servicio')
@section('page-description', 'Ubicar el expediente por número de resolución y de oferta')

@if($bloqueado)
<x-estado-bloqueado :titulo="$bloqueadoTitulo" :mensaje="$bloqueadoMensaje" :ruta-regreso="$bloqueadoRuta" />
@else
<div class="py-6 max-w-2xl mx-auto px-4">
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h2 class="text-lg font-bold mb-4 text-gray-800">Ubicar el expediente</h2>

        <form wire:submit.prevent="buscar" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Resolución</label>
                <input type="text" wire:model="codigoResolucion" placeholder="Ej. RES-2026-001"
                    class="w-full border border-gray-300 px-4 py-2 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('codigoResolucion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Oferta</label>
                <input type="text" wire:model="codigoOferta" placeholder="Ej. OFR-2026-001"
                    class="w-full border border-gray-300 px-4 py-2 rounded-md focus:ring-blue-500 focus:border-blue-500">
                @error('codigoOferta') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit" wire:loading.attr="disabled" wire:target="buscar"
                    class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="buscar">Verificar y Continuar</span>
                    <span wire:loading wire:target="buscar">Verificando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif