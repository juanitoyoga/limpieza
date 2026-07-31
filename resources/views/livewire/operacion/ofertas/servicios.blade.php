<div class="space-y-6">

    {{-- Servicios de la resolución (orden de compra del barrio) --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Servicios de la Resolución</h3>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th>Servicio</th>
                    <th>Cantidad</th>
                    <th>Costo Unitario</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($serviciosResolucion as $item)
                <tr class="border-b">
                    <td>{{ $item->catalogoServicio->nombre }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>{{ number_format($item->costo_unitario, 2) }}</td>
                    <td>
                        <button wire:click="agregarServicio({{ $item->id }})"
                            class="px-3 py-1 bg-blue-600 text-white rounded">
                            Agregar a Oferta
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Servicios agregados a la oferta --}}
    <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-3">Servicios de la Oferta</h3>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th>Servicio</th>
                    <th>Cantidad</th>
                    <th>Costo Unitario</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($serviciosOferta as $item)
                <tr class="border-b">
                    <td>{{ $item->catalogoServicio->nombre }}</td>

                    <td>
                        <input type="number" min="1"
                            wire:change="actualizarServicio({{ $item->id }}, 'cantidad', $event.target.value)"
                            value="{{ $item->cantidad }}"
                            class="w-20 border rounded px-2 py-1">
                    </td>

                    <td>
                        <input type="number" step="0.01"
                            wire:change="actualizarServicio({{ $item->id }}, 'costo_unitario', $event.target.value)"
                            value="{{ $item->costo_unitario }}"
                            class="w-24 border rounded px-2 py-1">
                    </td>

                    <td>{{ number_format($item->subtotal, 2) }}</td>

                    <td>
                        <button wire:click="eliminarServicio({{ $item->id }})"
                            class="px-3 py-1 bg-red-600 text-white rounded">
                            Eliminar
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mt-4">
            <span class="text-lg font-bold">
                Total Oferta: {{ number_format($oferta->monto_total, 2) }} €
            </span>
        </div>
    </div>

</div>