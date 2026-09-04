{{-- aprobar.blade.php --}}
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- Encabezado principal --}}
    <div class="md:flex md:items-center md:justify-between border-b border-gray-200 pb-4">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
                Aprobar Oferta: <span class="text-green-600">{{ $oferta->codigo }}</span>
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
                        <td class="px-6 py-3 text-right font-bold text-green-600 text-base">
                            ${{ number_format($oferta->monto_total ?? $oferta->ofertaServicios->sum('subtotal'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{-- Forma de Pago de la Oferta --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-bold mb-4">Forma de Pago Propuesta</h3>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3">#</th>
                    <th class="p-3">Tipo</th>
                    <th class="p-3">Servicio</th>
                    <th class="p-3">Valor</th>
                    <th class="p-3">Monto calculado</th>
                    <th class="p-3">Descripción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($oferta->formaPago as $linea)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 text-gray-400">{{ $linea->orden }}</td>
                    <td class="p-3">
                        <span @class([ 'px-2 py-0.5 rounded text-xs font-medium' , 'bg-blue-100 text-blue-700'=> $linea->tipo === 'anticipo',
                            'bg-purple-100 text-purple-700' => $linea->tipo === 'contra_servicio',
                            'bg-teal-100 text-teal-700' => $linea->tipo === 'saldo_final',
                            ])>
                            {{ match($linea->tipo) {
                            'anticipo' => 'Anticipo',
                            'contra_servicio' => 'Contra servicio',
                            'saldo_final' => 'Saldo final',
                            default => $linea->tipo,
                        } }}
                        </span>
                    </td>
                    <td class="p-3">{{ $linea->catalogoServicio?->nombre ?? '—' }}</td>
                    <td class="p-3">
                        {{ $linea->tipo_valor === 'porcentaje' ? $linea->valor . '%' : '$' . number_format($linea->valor, 2) }}
                    </td>
                    <td class="p-3 font-medium">${{ number_format($linea->montoEsperado($oferta), 2) }}</td>
                    <td class="p-3 text-gray-500">{{ $linea->descripcion ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">
                        Esta oferta no tiene forma de pago registrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Formulario y Cláusula de Aprobación --}}
    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <h3 class="text-md font-semibold text-gray-800 border-b pb-2">Confirmación y Aprobación Legal</h3>

        {{-- Cláusula legal --}}
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-gavel text-amber-600 mt-0.5"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-bold text-amber-800">Cláusula de Responsabilidad Legal</h4>
                    <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                        Al aprobar esta oferta, declaro bajo mi responsabilidad que la documentación presentada cumple con la normativa legal aplicable y con los requerimientos estipulados en la Resolución <strong>{{ $oferta->resolucion->codigo }}</strong>. Entiendo que esta acción descalifica y rechaza automáticamente el resto de ofertas competidoras vinculadas a dicha resolución y registrará un evento inmutable en el sistema de auditoría y blockchain.
                    </p>
                </div>
            </div>
        </div>

        {{-- Visto bueno (Checkbox) --}}
        <div>
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" wire:model="aceptaClausula" class="mt-1 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="text-sm text-gray-700 font-medium">
                    He leído, comprendo y acepto la cláusula de aprobación legal.
                </span>
            </label>
            @error('aceptaClausula')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Observaciones --}}
        <div>
            <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                Observaciones de Aprobación (opcional):
            </label>
            <textarea id="observaciones"
                wire:model="observaciones"
                rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-sm p-2 border"
                placeholder="Añada cualquier detalle sobre el dictamen o justificación de aprobación..."></textarea>
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

            <button wire:click="aprobar" class="inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium shadow-sm transition">
                Aprobar Oferta
            </button>
        </div>
    </div>

</div>