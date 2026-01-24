<div class="p-6 bg-white rounded shadow">

    <h2 class="text-lg font-semibold mb-4">
        Imprimir documento de nominación
    </h2>

    <p class="text-sm text-slate-600 mb-6">
        Este documento debe ser impreso, firmado y sellado para tener validez legal.
    </p>

    <div class="space-y-2 text-sm">
        <p><strong>Número de trámite:</strong> {{ $nomination->numero_tramite }}</p>
        <p><strong>Estado:</strong> {{ $nomination->estado }}</p>
        <p><strong>Candidato:</strong> {{ $nomination->candidate->last_name . ' ' . $nomination->candidate->first_name }}</p>
    </div>

    <div class="pt-6 border-t flex flex-col sm:flex-row gap-3">

        {{-- Regresar --}}
        <a href="{{ route('nominations.index') }}"
           class="bg-yellow-600 hover:bg-green-700 text-black px-4 py-2 rounded text-center">
            <i class="fas fa-arrow-left mr-2"></i> Regresar
        </a>
    
        @if(isset($path))
            <a href="{{ route('descargar-documento', $path) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center">
                Descargar documento
            </a>
        @else
            <button
                wire:click="imprimir"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Generar documento
            </button>
        @endif
    
    </div>
    

    
</div>