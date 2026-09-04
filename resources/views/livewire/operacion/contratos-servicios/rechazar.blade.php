@section('page-title', 'Rechazar Contrato')
@section('page-description', 'Rechazo del contrato de servicio')

@if($bloqueado)
<x-estado-bloqueado :titulo="$bloqueadoTitulo" :mensaje="$bloqueadoMensaje" :ruta-regreso="$bloqueadoRuta" />
@else
<div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-200 max-w-xl mx-auto">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-times-circle mr-2 text-red-500"></i> Rechazar Contrato {{ $contrato->codigo }}
    </h2>

    @error('global')
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">{{ $message }}</div>
    @enderror

    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label class="block text-sm font-semibold mb-2">Motivo del rechazo *</label>
            <textarea wire:model="observaciones" rows="4"
                placeholder="Explique por qué se rechaza este contrato..."
                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
            @error('observaciones') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-center gap-4">
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white rounded-lg font-semibold shadow-md">
                <i class="fas fa-times-circle mr-2"></i> RECHAZAR
            </button>
            <a href="{{ route('contratos-servicios.lista') }}"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Regresar
            </a>
        </div>
    </form>
</div>
@endif