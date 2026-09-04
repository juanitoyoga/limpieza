@section('page-title', 'Gestión de Órdenes de Pago')
@section('page-description', 'Verificación y aprobación de órdenes de pago por contrato')

<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Órdenes de Pago</h2>
            <p class="text-sm text-gray-500">Contrato {{ $contrato->codigo }} — {{ $contrato->titulo }}</p>
        </div>
        <a href="{{ route('contratos-servicios.show', $contrato) }}" class="text-sm text-blue-600 hover:underline">
            ← Volver al contrato
        </a>
    </div>

    {{-- Resumen financiero --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Monto total del contrato</p>
            <p class="text-2xl font-bold text-gray-800">${{ number_format($contrato->monto_total, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Comprometido (autorizado + pagado)</p>
            <p class="text-2xl font-bold text-yellow-600">
                ${{ number_format($contrato->monto_total - $this->saldoRestante, 2) }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs text-gray-500 uppercase">Saldo restante</p>
            <p class="text-2xl font-bold {{ $this->saldoRestante > 0 ? 'text-green-600' : 'text-gray-400' }}">
                ${{ number_format($this->saldoRestante, 2) }}
            </p>
        </div>
    </div>

    {{-- Plan de forma de pago (heredado de la oferta) --}}
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Plan de forma de pago pactado</h3>

        @if($this->planFormaPago->isEmpty())
        <p class="text-sm text-gray-400 italic">Este contrato no tiene un plan de pago definido.</p>
        @else
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2 pr-4">#</th>
                    <th class="py-2 pr-4">Tipo</th>
                    <th class="py-2 pr-4">Servicio</th>
                    <th class="py-2 pr-4">Valor pactado</th>
                    <th class="py-2 pr-4">Monto esperado</th>
                    <th class="py-2 pr-4">Descripción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->planFormaPago as $linea)
                <tr class="border-b last:border-0">
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
                    <td class="py-2 pr-4 text-gray-600">
                        {{ $linea->tipo_valor === 'porcentaje' ? $linea->valor . '%' : '$' . number_format($linea->valor, 2) }}
                    </td>
                    <td class="py-2 pr-4 font-medium text-gray-800">
                        ${{ number_format($linea->montoEsperado($contrato), 2) }}
                    </td>
                    <td class="py-2 pr-4 text-gray-500">{{ $linea->descripcion ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Formulario de registro (Dirigente) --}}
    @can('ordenes-pago.registrar', $contrato)
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Registrar nueva orden de pago</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Línea del plan de pago</label>
                <select wire:change="seleccionarLineaPlan($event.target.value)"
                    class="w-full border-gray-300 rounded-md text-sm">
                    <option value="">-- Seleccione una línea del plan --</option>
                    @foreach($this->planFormaPago as $linea)
                    <option value="{{ $linea->id }}" {{ $contratoFormaPagoId == $linea->id ? 'selected' : '' }}>
                        {{ match($linea->tipo) {
                                    'anticipo' => 'Anticipo',
                                    'contra_servicio' => 'Contra servicio: ' . ($linea->catalogoServicio?->nombre ?? ''),
                                    'saldo_final' => 'Saldo final',
                                    default => $linea->tipo,
                                } }}
                        ({{ $linea->tipo_valor === 'porcentaje' ? $linea->valor . '%' : '$' . number_format($linea->valor, 2) }})
                    </option>
                    @endforeach
                </select>
                @error('tipo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1">Monto a emitir</label>
                <input type="number" step="0.01" wire:model="monto"
                    class="w-full border-gray-300 rounded-md text-sm">
                @error('monto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">Saldo disponible: ${{ number_format($this->saldoRestante, 2) }}</p>
            </div>
        </div>

        {{-- Selección de hitos (solo aparece si la línea es "contra_servicio") --}}
        @if($tipo === 'contra_servicio')
        <div class="mt-4">
            <label class="block text-xs text-gray-500 mb-2">Hitos aprobados disponibles</label>

            @if($this->hitosDisponibles->isEmpty())
            <p class="text-sm text-amber-600 bg-amber-50 rounded p-2">
                No hay hitos aprobados disponibles para incluir en esta orden.
            </p>
            @else
            <div class="space-y-2 max-h-48 overflow-y-auto border rounded p-2">
                @foreach($this->hitosDisponibles as $hito)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" value="{{ $hito->id }}"
                        wire:model="hitosSeleccionados"
                        class="rounded border-gray-300">
                    <span>
                        {{ $hito->detalle?->catalogoServicio?->nombre ?? 'Servicio sin nombre' }}
                        — {{ $hito->descripcion_servicio ?? 'Sin descripción' }}
                        <span class="text-gray-400">(aprobado {{ $hito->aprobado_at?->format('d/m/Y') }})</span>
                    </span>
                </label>
                @endforeach
            </div>
            @endif
            @error('hitosSeleccionados') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        @endif

        @if($tipo === 'anticipo' && $this->existeAnticipo)
        <p class="text-sm text-amber-600 bg-amber-50 rounded p-2 mt-3">
            Ya existe una orden de anticipo registrada para este contrato.
        </p>
        @endif

        <div class="mt-4">
            <label class="block text-xs text-gray-500 mb-1">Observaciones (opcional)</label>
            <textarea wire:model="observaciones" rows="2"
                class="w-full border-gray-300 rounded-md text-sm"></textarea>
        </div>

        <div class="mt-4 flex justify-end">
            <button wire:click="registrar" wire:loading.attr="disabled"
                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-medium px-4 py-2 rounded-md">
                <span wire:loading.remove wire:target="registrar">Registrar orden de pago</span>
                <span wire:loading wire:target="registrar">Guardando...</span>
            </button>
        </div>
    </div>
    @endcan

    {{-- Listado de órdenes de pago --}}
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Historial de órdenes de pago</h3>

        @if($this->ordenesPago->isEmpty())
        <p class="text-sm text-gray-400 italic">Aún no se han registrado órdenes de pago para este contrato.</p>
        @else
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="py-2 pr-4">#</th>
                    <th class="py-2 pr-4">Tipo</th>
                    <th class="py-2 pr-4">Monto</th>
                    <th class="py-2 pr-4">Estado</th>
                    <th class="py-2 pr-4">Registrada por</th>
                    <th class="py-2 pr-4">Hitos</th>
                    <th class="py-2 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->ordenesPago as $orden)
                <tr class="border-b last:border-0 align-top">
                    <td class="py-2 pr-4 text-gray-400">#{{ $orden->id }}</td>
                    <td class="py-2 pr-4 capitalize">{{ str_replace('_', ' ', $orden->tipo) }}</td>
                    <td class="py-2 pr-4 font-medium">${{ number_format($orden->monto, 2) }}</td>
                    <td class="py-2 pr-4">
                        <span class="px-2 py-0.5 rounded text-xs font-medium text-white {{ $orden->estadoColor() }}">
                            {{ $orden->estadoLabel() }}
                        </span>
                        @if($orden->estado === 'Anulada' && $orden->motivo_anulacion)
                        <p class="text-xs text-red-500 mt-1">{{ $orden->motivo_anulacion }}</p>
                        @endif
                        @if($orden->estado === 'Pagada' && $orden->referencia_pago)
                        <p class="text-xs text-gray-400 mt-1">Ref: {{ $orden->referencia_pago }}</p>
                        @endif
                    </td>
                    <td class="py-2 pr-4 text-gray-500">
                        {{ $orden->registrador?->name ?? '—' }}
                        <p class="text-xs text-gray-400">{{ $orden->fecha_registro?->format('d/m/Y H:i') }}</p>
                    </td>
                    <td class="py-2 pr-4 text-gray-500">
                        @forelse($orden->hitos as $hito)
                        <p class="text-xs">{{ $hito->detalle?->catalogoServicio?->nombre ?? 'Hito #' . $hito->id }}</p>
                        @empty
                        <span class="text-xs text-gray-300">—</span>
                        @endforelse
                    </td>
                    <td class="py-2 pr-4">
                        <div class="flex justify-end gap-2 flex-wrap">
                            @can('ordenes-pago.autorizar', $contrato)
                            @if($orden->puedeAutorizarse())
                            <button wire:click="autorizar({{ $orden->id }})"
                                wire:confirm="¿Autorizar esta orden de pago por ${{ number_format($orden->monto, 2) }}?"
                                class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md">
                                Autorizar
                            </button>
                            @endif
                            @endcan

                            @can('ordenes-pago.pagar', $contrato)
                            @if($orden->puedeMarcarsePagada())
                            <button wire:click="abrirMarcarPagada({{ $orden->id }})"
                                class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md">
                                Marcar pagada
                            </button>
                            @endif
                            @endcan

                            @can('ordenes-pago.anular', $contrato)
                            @if($orden->puedeAnularse())
                            <button wire:click="abrirAnular({{ $orden->id }})"
                                class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-md">
                                Anular
                            </button>
                            @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Modal: Marcar pagada --}}
    @if($ordenSeleccionadaId && $this->ordenesPago->firstWhere('id', $ordenSeleccionadaId)?->puedeMarcarsePagada())
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" wire:key="modal-pagar">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirmar pago</h3>
            <label class="block text-xs text-gray-500 mb-1">Referencia de pago (opcional)</label>
            <input type="text" wire:model="referenciaPago"
                placeholder="N° de transferencia, comprobante, etc."
                class="w-full border-gray-300 rounded-md text-sm mb-4">
            @error('referenciaPago') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror

            <div class="flex justify-end gap-2">
                <button wire:click="$set('ordenSeleccionadaId', null)"
                    class="text-sm px-4 py-2 rounded-md text-gray-600 hover:bg-gray-100">
                    Cancelar
                </button>
                <button wire:click="confirmarPago" wire:loading.attr="disabled"
                    class="text-sm px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white">
                    Confirmar pago
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: Anular --}}
    @if($ordenSeleccionadaId && $this->ordenesPago->firstWhere('id', $ordenSeleccionadaId)?->puedeAnularse())
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" wire:key="modal-anular">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Anular orden de pago</h3>
            <label class="block text-xs text-gray-500 mb-1">Motivo de anulación</label>
            <textarea wire:model="motivoAnulacion" rows="3"
                class="w-full border-gray-300 rounded-md text-sm mb-1"></textarea>
            @error('motivoAnulacion') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror

            <div class="flex justify-end gap-2 mt-4">
                <button wire:click="$set('ordenSeleccionadaId', null)"
                    class="text-sm px-4 py-2 rounded-md text-gray-600 hover:bg-gray-100">
                    Cancelar
                </button>
                <button wire:click="confirmarAnulacion" wire:loading.attr="disabled"
                    class="text-sm px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white">
                    Anular orden
                </button>
            </div>
        </div>
    </div>
    @endif

</div>