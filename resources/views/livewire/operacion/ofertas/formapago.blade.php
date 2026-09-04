@section('page-title', 'Ofertas')
@section('page-description', 'Mantenimiento Formas de Pago')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Forma de pago de la oferta</h2>
            <p class="text-sm text-gray-500">Oferta {{ $oferta->codigo }} — {{ $oferta->titulo }}</p>
        </div>
        <a href="{{ route('ofertas.show', $oferta) }}" class="text-sm text-blue-600 hover:underline">
            ← Volver a la oferta
        </a>
    </div>
    {{-- Resumen de servicios y monto total de la oferta --}}
    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Servicios de la oferta</h3>

        @if($serviciosOferta->isEmpty())
        <p class="text-sm text-gray-400 italic">Sin servicios agregados.</p>
        @else
        <table class="min-w-full text-sm mb-3">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-1.5 pr-4">Servicio</th>
                    <th class="py-1.5 pr-4 text-right">Cantidad</th>
                    <th class="py-1.5 pr-4 text-right">Costo unitario</th>
                    <th class="py-1.5 pr-4 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviciosOferta as $servicio)
                <tr class="border-b last:border-0">
                    <td class="py-1.5 pr-4 text-gray-700">
                        {{ $servicio->catalogoServicio?->nombre ?? 'Servicio #' . $servicio->catalogo_servicio_id }}
                    </td>
                    <td class="py-1.5 pr-4 text-right text-gray-600">{{ $servicio->cantidad }}</td>
                    <td class="py-1.5 pr-4 text-right text-gray-600">${{ number_format($servicio->costo_unitario, 2) }}</td>
                    <td class="py-1.5 pr-4 text-right font-medium text-gray-800">${{ number_format($servicio->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="flex justify-end border-t pt-2">
            <span class="text-sm font-semibold text-gray-800">
                Total de la oferta: ${{ number_format($this->totalOferta(), 2) }}
            </span>
        </div>
    </div>
    {{-- Aviso si aún no hay servicios cargados --}}
    @if($serviciosOferta->isEmpty())
    <div class="bg-amber-50 border border-amber-200 text-amber-700 text-sm rounded-lg p-4">
        Esta oferta todavía no tiene servicios agregados. Para crear una línea de pago
        <strong>"contra servicio"</strong> primero debes
        <a href="{{ route('ofertas.servicios', $oferta) }}" class="underline font-medium">agregar los servicios</a>.
    </div>
    @endif

    {{-- Formulario de nueva línea --}}
    <div class="bg-white rounded-lg shadow p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 border-b pb-2">Agregar línea de pago</h3>


        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tipo de pago <span class="text-red-500">*</span></label>
                <select wire:model.live="nuevoTipo" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione --</option>
                    <option value="anticipo">Anticipo</option>
                    <option value="contra_servicio">Contra servicio concluido</option>
                    <option value="saldo_final">Saldo final</option>
                </select>
            </div>

            @if($nuevoTipo === 'contra_servicio')
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Servicio asociado <span class="text-red-500">*</span>
                </label>
                <select wire:model="nuevoCatalogoServicioId" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccione servicio --</option>
                    @foreach($serviciosOferta as $servicio)
                    <option value="{{ $servicio->catalogo_servicio_id }}">
                        {{ $servicio->catalogoServicio?->nombre ?? 'Servicio #' . $servicio->catalogo_servicio_id }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif


            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tipo de valor</label>
                <select wire:model.live="nuevoTipoValor" class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="porcentaje">Porcentaje (%)</option>
                    <option value="monto_fijo">Monto fijo ($)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Valor {{ $nuevoTipoValor === 'porcentaje' ? '(%)' : '($)' }} <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" wire:model.live="nuevoValor"
                    placeholder="{{ $nuevoTipoValor === 'porcentaje' ? 'Ej. 30' : 'Ej. 500.00' }}"
                    class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Descripción corta (opcional)</label>
                <input type="text" wire:model="nuevaDescripcion"
                    placeholder="Ej. Pago correspondiente a la primera entrega del hito"
                    class="w-full border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Manejo de errores --}}
        @error('nuevoTipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('nuevoCatalogoServicioId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('nuevoTipoValor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('nuevoValor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        {{-- Totales en vivo — se recalculan mientras el usuario completa el formulario --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 bg-gray-50 rounded-lg p-3 border border-gray-200">
            <div>
                <p class="text-xs text-gray-500">Ya asignado en el plan</p>
                <p class="text-sm font-semibold text-gray-800">${{ number_format($this->totalAsignadoEnPlan(), 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">
                    + Línea en edición
                    @if($this->montoPreviewNuevaLinea() > 0)
                    <span class="text-blue-500">(previsualización)</span>
                    @endif
                </p>
                <p class="text-sm font-semibold text-blue-600">${{ number_format($this->montoPreviewNuevaLinea(), 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Saldo pendiente de asignar</p>
                <p class="text-sm font-semibold {{ $this->saldoPendiente() < 0 ? 'text-red-600' : 'text-green-600' }}">
                    ${{ number_format($this->saldoPendiente(), 2) }}
                </p>
                @if($this->saldoPendiente() < 0)
                    <p class="text-xs text-red-500 mt-0.5">El plan excede el total de la oferta</p>
                    @endif
            </div>
        </div>
        {{-- Botón movido a su propia fila, ancho completo del formulario --}}
        <div class="mt-4 flex justify-end">
            <button wire:click="agregarLineaDesdeFormulario" wire:loading.attr="disabled"
                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-medium py-2 px-4 rounded-md transition shadow-sm">
                <span wire:loading.remove wire:target="agregarLineaDesdeFormulario">+ Agregar línea</span>
                <span wire:loading wire:target="agregarLineaDesdeFormulario">Guardando...</span>
            </button>
        </div>
    </div>

    {{-- Tabla de líneas ya agregadas --}}
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Plan de pago actual</h3>
            <span class="text-xs text-gray-400">
                Suma de líneas en porcentaje: {{ number_format($this->sumaPorcentajes(), 2) }}%
            </span>
        </div>

        @if($lineasFormaPago->isEmpty())
        <p class="text-sm text-gray-400 italic">Aún no se han agregado líneas de forma de pago.</p>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-4">#</th>
                        <th class="py-2 pr-4">Tipo</th>
                        <th class="py-2 pr-4">Servicio</th>
                        <th class="py-2 pr-4">Tipo valor</th>
                        <th class="py-2 pr-4">Valor</th>
                        <th class="py-2 pr-4 text-right">Monto calculado</th>
                        <th class="py-2 pr-4">Descripción</th>
                        <th class="py-2 pr-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lineasFormaPago as $linea)
                    <tr class="border-b last:border-0" wire:key="linea-pago-{{ $linea->id }}">
                        <td class="py-2 pr-4 text-gray-400">{{ $linea->orden }}</td>
                        <td class="py-2 pr-4">
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
                        <td class="py-2 pr-4 text-gray-600">
                            {{ $linea->catalogoServicio?->nombre ?? '—' }}
                        </td>
                        <td class="py-2 pr-4">
                            <select
                                wire:change="actualizarLinea({{ $linea->id }}, 'tipo_valor', $event.target.value)"
                                class="border-gray-300 rounded-md text-xs py-1">
                                <option value="porcentaje" {{ $linea->tipo_valor === 'porcentaje' ? 'selected' : '' }}>%</option>
                                <option value="monto_fijo" {{ $linea->tipo_valor === 'monto_fijo' ? 'selected' : '' }}>$</option>
                            </select>
                        </td>
                        <td class="py-2 pr-4">
                            <input type="number" step="0.01" value="{{ $linea->valor }}"
                                wire:change="actualizarLinea({{ $linea->id }}, 'valor', $event.target.value)"
                                class="w-24 border-gray-300 rounded-md text-xs py-1">
                        </td>
                        <td class="py-2 pr-4 text-right font-medium text-gray-700">
                            ${{ number_format($linea->montoEsperado($oferta), 2) }}
                        </td>
                        <td class="py-2 pr-4">
                            <input type="text" value="{{ $linea->descripcion }}"
                                wire:change="actualizarLinea({{ $linea->id }}, 'descripcion', $event.target.value)"
                                class="w-full border-gray-300 rounded-md text-xs py-1">
                        </td>
                        <td class="py-2 pr-4 text-right">
                            <button wire:click="eliminarLinea({{ $linea->id }})"
                                wire:confirm="¿Eliminar esta línea de forma de pago?"
                                class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-md">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>