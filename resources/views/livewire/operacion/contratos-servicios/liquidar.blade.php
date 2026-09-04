@section('page-title', 'Liquidar Contrato')
@section('page-description', 'Liquidación del contrato de servicio')

@if($bloqueado)
<x-estado-bloqueado :titulo="$bloqueadoTitulo" :mensaje="$bloqueadoMensaje" :ruta-regreso="$bloqueadoRuta" />
@else
<div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 max-w-xl mx-auto">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-times-circle mr-2 text-red-500"></i> Liquidar Contrato {{ $contrato->codigo }}
    </h2>

    @error('global')
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">{{ $message }}</div>
    @enderror

    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label class="block text-sm font-semibold mb-2">Motivo de la liquidación *</label>
            <textarea wire:model="observaciones" rows="4"
                placeholder="Explique por qué se liquidará este contrato..."
                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
            @error('observaciones') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Documento de respaldo (PDF, máx. 5MB) *</label>
            <input type="file" wire:model="documento_pdf" accept="application/pdf" class="w-full border px-4 py-2 rounded bg-gray-50">
            <div wire:loading wire:target="documento_pdf" class="text-sm text-blue-600 mt-1">Subiendo documento...</div>
            @error('documento_pdf') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>
        <div class="flex justify-center gap-4">
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white rounded-lg font-semibold shadow-md">
                <i class="fas fa-times-circle mr-2"></i> LIQUIDAR
            </button>
            <a href="{{ route('contratos-servicios.lista') }}"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Regresar
            </a>
        </div>
    </form>
</div>
@endif