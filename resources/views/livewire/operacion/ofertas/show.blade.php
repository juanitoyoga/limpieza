<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalle de Oferta: {{ $oferta->codigo ?? '#' . $oferta->id }}</h1>
            <a href="{{ route('ofertas.index') }}" class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-50">Volver</a>
        </div>

        <!-- Encabezado de la Oferta -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <span class="block text-sm text-gray-500">Proveedor</span>
                    <span class="font-bold text-gray-800">{{ $oferta->proveedor->razon_social }}</span>
                </div>
                <div>
                    <span class="block text-sm text-gray-500">Resolución</span>
                    <span class="font-bold text-gray-800">{{ $oferta->resolucion->codigo ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="block text-sm text-gray-500">Estado</span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full text-white {{ $oferta->estadoColor() }}">
                        {{ $oferta->estadoLabel() }}
                    </span>
                </div>
                <div>
                    <span class="block text-sm text-gray-500">Monto Total</span>
                    <span class="text-xl font-bold text-blue-600">${{ number_format($oferta->monto_total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Servicios Ofertados -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Servicios Involucrados</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Servicio Catálogo</th>
                        <th class="px-4 py-2 text-center text-xs text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Costo Unit.</th>
                        <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Subtotal</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Observación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($oferta->ofertaServicios as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->catalogoServicio->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->cantidad }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($item->costo_unitario, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">${{ number_format($item->subtotal, 2) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->observaciones ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>