@section('page-title', $oferta->codigo)
@section('page-description', 'Detalle de la oferta')

<div class="space-y-6">
    @if (session('success'))
    <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    {{-- Info de la Oferta --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-bold">{{ $oferta->titulo }}</h2>
                <p class="text-sm text-gray-500">Código: {{ $oferta->codigo }}</p>
            </div>
            <span class="px-3 py-1 rounded text-white text-sm {{ $oferta->estadoColor() }}">
                {{ $oferta->estadoLabel() }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-4">
            <div><span class="text-gray-500">Proveedor:</span> {{ $oferta->proveedor->razon_social }}</div>
            <div><span class="text-gray-500">Resolución:</span> {{ $oferta->resolucion->codigo }}</div>
            <div><span class="text-gray-500">Fecha Presentación:</span> {{ $oferta->fecha_presentacion?->format('d/m/Y') ?? '—' }}</div>
            <div><span class="text-gray-500">Monto Total:</span> ${{ number_format($oferta->monto_total, 2) }}</div>
            <div><span class="text-gray-500">Hash Blockchain:</span>
                {{ $oferta->tx_hash ? substr($oferta->tx_hash, 0, 10) . '...' : '—' }}
            </div>
            @if ($oferta->verificador)
            <div><span class="text-gray-500">Verificada por:</span> {{ $oferta->verificador->last_name }} el {{ $oferta->fecha_verificacion?->format('d/m/Y H:i') }}</div>
            @endif
            @if ($oferta->aprobador)
            <div><span class="text-gray-500">Aprobada por:</span> {{ $oferta->aprobador->last_name }} el {{ $oferta->fecha_aprobacion?->format('d/m/Y H:i') }}</div>
            @endif
            @if ($oferta->rechazador)
            <div><span class="text-gray-500">Rechazada por:</span> {{ $oferta->rechazador->last_name }} el {{ $oferta->fecha_rechazo?->format('d/m/Y H:i') }}</div>
            @endif
        </div>

        @if($oferta->descripcion)
        <div class="text-sm border-t pt-3 mb-4">
            <span class="text-gray-500 block mb-1">Descripción:</span>
            <p class="text-gray-700">{{ $oferta->descripcion }}</p>
        </div>
        @endif

        @if($oferta->observaciones)
        <div class="text-sm border-t pt-3 mb-4">
            <span class="text-gray-500 block mb-1">Observaciones:</span>
            <p class="text-gray-700 whitespace-pre-line">{{ $oferta->observaciones }}</p>
        </div>
        @endif

        {{-- Acciones condicionadas al estado + Gate --}}
        <div class="flex flex-wrap gap-2 border-t pt-4">
            @if ($this->puedeVerDocumento())
            <a href="{{ route('ver.documento', ['disco' => 'ofertas', 'path' => base64_encode($oferta->documento_original_path)]) }}"
                target="_blank"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                <i class="fas fa-file-pdf mr-1"></i> Ver Documento
            </a>
            @endif

            @if ($this->puedeEditarServicios())
            <a href="{{ route('ofertas.servicios', $oferta) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Editar Servicios
            </a>

            <a href="{{ route('ofertas.formapago', $oferta) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                Editar Forma de Pago
            </a>
            @endif

            @if ($this->puedeVerificar())
            <a href="{{ route('ofertas.verificar', $oferta) }}"
                class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                Verificar
            </a>
            @endif

            @if ($this->puedeAprobar())
            <a href="{{ route('ofertas.aprobar', $oferta) }}"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                Aprobar
            </a>
            @endif

            @if ($this->puedeRechazar())
            <a href="{{ route('ofertas.rechazar', $oferta) }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                Rechazar
            </a>
            @endif
        </div>
    </div>

    {{-- Servicios de la Oferta (solo lectura aquí; edición vía "Editar Servicios") --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-bold mb-4">Servicios de la Oferta</h3>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3">Servicio</th>
                    <th class="p-3">Cantidad</th>
                    <th class="p-3">Costo Unitario</th>
                    <th class="p-3">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($oferta->ofertaServicios as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $item->catalogoServicio->nombre }}</td>
                    <td class="p-3">{{ $item->cantidad }}</td>
                    <td class="p-3">${{ number_format($item->costo_unitario, 2) }}</td>
                    <td class="p-3">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">
                        No hay servicios agregados a esta oferta.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="text-right mt-4 font-bold text-lg">
            Total: ${{ number_format($oferta->monto_total, 2) }}
        </div>
    </div>
    {{-- Forma de Pago de la Oferta (solo lectura aquí; edición vía "Editar Forma de Pago") --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-bold mb-4">Forma de Pago de la Oferta</h3>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3">#</th>
                    <th class="p-3">Tipo</th>
                    <th class="p-3">Servicio</th>
                    <th class="p-3">Tipo valor</th>
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
                    <td class="p-3">{{ $linea->tipo_valor === 'porcentaje' ? 'Porcentaje' : 'Monto fijo' }}</td>
                    <td class="p-3">
                        {{ $linea->tipo_valor === 'porcentaje' ? $linea->valor . '%' : '$' . number_format($linea->valor, 2) }}
                    </td>
                    <td class="p-3 font-medium">${{ number_format($linea->montoEsperado($oferta), 2) }}</td>
                    <td class="p-3 text-gray-500">{{ $linea->descripcion ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        No hay líneas de forma de pago agregadas a esta oferta.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($oferta->formaPago->isNotEmpty())
        <div class="text-right mt-4 font-bold text-lg">
            Total asignado: ${{ number_format($oferta->formaPago->sum(fn($l) => $l->montoEsperado($oferta)), 2) }}
            / ${{ number_format($oferta->monto_total, 2) }}
        </div>
        @endif
    </div>
    {{-- Historial de Auditoría --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-bold mb-4">Historial de Auditoría</h3>

        <div class="space-y-2">
            @forelse ($historial as $evento)
            <div class="flex items-start gap-3 text-sm border-b pb-2">
                <span>{{ $evento->event_icon }}</span>
                <div class="flex-1">
                    <span class="font-medium">{{ $evento->event_type_name }}</span>
                    <span class="text-gray-500"> — {{ $evento->event_at?->format('d/m/Y H:i') }}</span>
                    @if ($evento->tx_hash)
                    <span class="text-xs text-gray-400 block">tx: {{ substr($evento->tx_hash, 0, 10) }}...</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">Sin eventos registrados.</p>
            @endforelse
        </div>
    </div>
</div>