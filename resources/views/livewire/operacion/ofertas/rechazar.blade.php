{{-- rechazar.blade.php --}}
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- Encabezado principal --}}
    <div class="md:flex md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
                Rechazar Oferta: <span class="text-red-600">{{ $oferta->codigo }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Título: <span class="font-medium text-gray-700">{{ $oferta->titulo }}</span>
            </p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white {{ $oferta->estadoColor() }}">
                {{ $oferta->estadoLabel() }}
            </span>
        </div>
    </div>

    {{-- Resumen de Información General --}}
    <div class="bg-white shadow rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-md font-semibold text-gray-800 border-b pb-2 mb-3">Información General</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 font-medium">Proveedor:</dt>
                    <dd class="text-gray-900 font-semibold">{{ $oferta->proveedor->razon_social ?? $oferta->proveedor->nombre ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 font-medium">Resolución Asociada:</dt>
                    <dd class="text-gray-900 font-medium">{{ $oferta->resolucion->codigo ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 font-medium">Fecha de Presentación:</dt>
                    <dd class="text-gray-900">{{ $oferta->fecha_presentacion ? $oferta->fecha_presentacion->format('d/m/Y') : 'N/A' }}</dd>
                </div>
                @if($oferta->verificador)
                <div class="flex justify-between pt-1 border-t border-gray-100">
                    <dt class="text-gray-500 font-medium">Verificado por:</dt>
                    <dd class="text-gray-800">{{ $oferta->verificador->name ?? 'Usuario N/A' }} ({{ $oferta->fecha_verificacion ? $oferta->fecha_verificacion->format('d/m/Y H:i') : '' }})</dd>
                </div>
                @endif
            </dl>
            @if($oferta->descripcion)
            <div class="mt-3 pt-3 border-t">
                <dt class="text-xs text-gray-500 font-medium uppercase">Descripción:</dt>
                <dd class="text-sm text-gray-700 mt-1">{{ $oferta->descripcion }}</dd>
            </div>
            @endif
        </div>

        <div>
            <h3 class="text-md font-semibold text-gray-800 border-b pb-2 mb-3">Documentación y Blockchain</h3>
            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="text-gray-500 font-medium">Hash del Documento Original:</dt>
                    <dd class="text-xs font-mono bg-gray-100 p-2 rounded break-all mt-1 border text-gray-800">
                        {{ $oferta->documento_original_hash ?? 'Sin registrar' }}
                    </dd>
                </div>
                @if($oferta->tx_hash)
                <div class="pt-2">
                    <dt class="text-gray-500 font-medium">Tx Hash Blockchain:</dt>
                    <dd class="text-xs font-mono bg-gray-100 p-1.5 rounded break-all text-gray-700">
                        {{ $oferta->tx_hash }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Desglose de Servicios Ofertados --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-md font-semibold text-gray-800">Servicios Incluidos en la Oferta</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Unitario</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($oferta->ofertaServicios as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                            <div class="font-medium">{{ $item->catalogoServicio->nombre ?? 'Servicio #' . $item->catalogo_servicio_id }}</div>
                            @if($item->observaciones)
                            <div class="text-xs text-gray-500">{{ $item->observaciones }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-700">
                            {{ number_format($item->cantidad) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-700">
                            ${{ number_format($item->costo_unitario, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-gray-900">
                            ${{ number_format($item->subtotal, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 italic">
                            No hay servicios registrados en esta oferta.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-900">
                            Monto Total de la Oferta:
                        </td>
                        <td class="px-6 py-3 text-right font-bold text-red-600 text-base">
                            ${{ number_format($oferta->monto_total ?? $oferta->ofertaServicios->sum('subtotal'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Formulario de Rechazo --}}
    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <h3 class="text-md font-semibold text-gray-800 border-b pb-2">Confirmación de Rechazo</h3>

        {{-- Alerta informativa --}}
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-bold text-red-800">Advertencia de Rechazo</h4>
                    <p class="text-xs text-red-700 mt-1 leading-relaxed">
                        Esta acción cambiará el estado de la oferta a <strong>Rechazada</strong> y quedará registrada formalmente en la auditoría del sistema y en blockchain. Debe ingresar una justificación clara del motivo de rechazo.
                    </p>
                </div>
            </div>
        </div>

        {{-- Motivo del Rechazo / Observaciones --}}
        <div>
            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                Motivo del Rechazo <span class="text-red-500">*</span>:
            </label>
            <textarea id="observaciones"
                wire:model="observaciones"
                rows="4"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-sm p-2 border"
                placeholder="Escriba detalladamente el motivo o las observaciones que sustentan el rechazo de esta oferta..."></textarea>
            @error('observaciones')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Acciones --}}
        <div class="pt-4 border-t flex flex-col sm:flex-row justify-end gap-3">
            @if($oferta->documento_original_path)
            <a href="{{ route('ver.documento', ['disco' => 'ofertas', 'path' => base64_encode($oferta->documento_original_path)]) }}"
                target="_blank"
                class="inline-flex justify-center items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm font-medium shadow-sm transition">
                <i class="fas fa-file-pdf mr-2"></i> Ver Documento
            </a>
            @endif

            <a href="{{ route('ofertas.lista') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm font-medium transition">
                Cancelar
            </a>

            <button wire:click="confirmar" class="inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium shadow-sm transition">
                Confirmar Rechazo
            </button>
        </div>
    </div>

</div>