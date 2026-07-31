<div class="bg-white p-6 rounded shadow space-y-6">

    <h2 class="text-xl font-bold">Rechazar Oferta</h2>

    <div class="border p-4 rounded bg-gray-50">
        <p><strong>Código:</strong> {{ $oferta->codigo }}</p>
        <p><strong>Proveedor:</strong> {{ $oferta->proveedor->nombre }}</p>
        <p><strong>Resolución:</strong> {{ $oferta->resolucion->codigo }}</p>
        <p><strong>Monto Total:</strong> {{ number_format($oferta->monto_total, 2) }} €</p>
    </div>

    <div>
        <label class="font-semibold">Motivo del Rechazo</label>
        <textarea wire:model="observaciones"
            class="w-full border rounded p-2"
            rows="4"></textarea>
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('ofertas.lista') }}"
            class="px-4 py-2 bg-gray-200 rounded">
            Cancelar
        </a>

        <button wire:click="rechazar"
            class="px-4 py-2 bg-red-600 text-white rounded">
            Rechazar Oferta
        </button>
    </div>

</div>