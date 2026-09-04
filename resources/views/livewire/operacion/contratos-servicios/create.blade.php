@section('page-title', 'Generar Contrato de Servicio')
@section('page-description', $ofertaId
? 'A partir de la oferta ' . $this->oferta->codigo
: 'Ubicar el expediente por número de resolución y de oferta')

<div class="py-6 max-w-4xl mx-auto px-4 space-y-6">

    @error('global')
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
        {{ $message }}
    </div>
    @enderror

    @if (is_null($ofertaId))
    {{-- FASE 1: Búsqueda del expediente --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200 max-w-2xl mx-auto">
        <h2 class="text-lg font-bold mb-4 text-gray-800">Ubicar el expediente</h2>

        <form wire:submit.prevent="buscar" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Resolución</label>
                <input type="text" wire:model="codigoResolucion" placeholder="Ej. RES-2026-001"
                    class="w-full border border-gray-300 px-4 py-2 rounded-md">
                @error('codigoResolucion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Oferta</label>
                <input type="text" wire:model="codigoOferta" placeholder="Ej. OFR-2026-001"
                    class="w-full border border-gray-300 px-4 py-2 rounded-md">
                @error('codigoOferta') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit" wire:loading.attr="disabled" wire:target="buscar"
                    class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="buscar">Verificar y Continuar</span>
                    <span wire:loading wire:target="buscar">Verificando...</span>
                </button>
            </div>
        </form>
    </div>

    @else
    {{-- FASE 2: Datos y documento del contrato --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex items-center justify-between mb-6 border-b pb-4">
            <h2 class="text-lg font-bold text-gray-800">Datos y Documento del Contrato</h2>
            <button type="button" wire:click="cambiarExpediente" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-times mr-1"></i> Cambiar expediente
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div><span class="text-gray-500">Resolución:</span> <strong>{{ $this->oferta->resolucion->codigo ?? '—' }}</strong></div>
            <div><span class="text-gray-500">Oferta:</span> <strong>{{ $this->oferta->codigo }}</strong></div>
            <div><span class="text-gray-500">Proveedor:</span> <strong>{{ $this->oferta->proveedor->razon_social ?? $this->oferta->proveedor->nombre ?? '—' }}</strong></div>
            <div><span class="text-gray-500">Monto Total:</span> <strong>${{ number_format($this->oferta->monto_total, 2) }}</strong></div>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código del Contrato *</label>
                    <input type="text" wire:model="codigo" placeholder="Ej. CON-2026-001" class="w-full border border-gray-300 px-4 py-2 rounded-md">
                    @error('codigo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título del Contrato *</label>
                    <input type="text" wire:model="titulo" class="w-full border border-gray-300 px-4 py-2 rounded-md">
                    @error('titulo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea wire:model="descripcion" rows="3" class="w-full border border-gray-300 px-4 py-2 rounded-md"></textarea>
                @error('descripcion') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio *</label>
                    <input type="date" wire:model="fecha_inicio" class="w-full border border-gray-300 px-4 py-2 rounded-md">
                    @error('fecha_inicio') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin Estimada</label>
                    <input type="date" wire:model="fecha_fin_estimada" class="w-full border border-gray-300 px-4 py-2 rounded-md">
                    @error('fecha_fin_estimada') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento del Contrato Firmado (PDF, máx. 5MB) *</label>
                <input type="file" wire:model="documento_pdf" accept="application/pdf" class="w-full border border-gray-300 px-4 py-2 rounded-md bg-gray-50">
                <div wire:loading wire:target="documento_pdf" class="text-sm text-blue-600 mt-1">Subiendo documento...</div>
                @error('documento_pdf') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>


            {{-- Preview de servicios de la oferta ganadora --}}
            <div class="bg-white rounded-lg shadow p-5 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">
                    Servicios a copiar desde la oferta {{ $this->oferta->codigo }}
                </h3>

                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">Servicio</th>
                            <th class="p-2 text-right">Cantidad</th>
                            <th class="p-2 text-right">Costo Unitario</th>
                            <th class="p-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->oferta->ofertaServicios as $item)
                        <tr class="border-b">
                            <td class="p-2">{{ $item->catalogoServicio?->nombre ?? '—' }}</td>
                            <td class="p-2 text-right">{{ $item->cantidad }}</td>
                            <td class="p-2 text-right">${{ number_format($item->costo_unitario, 2) }}</td>
                            <td class="p-2 text-right font-medium">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-right mt-3 font-bold">
                    Total: ${{ number_format($this->oferta->monto_total, 2) }}
                </div>
            </div>

            {{-- Preview de forma de pago de la oferta ganadora --}}
            <div class="bg-white rounded-lg shadow p-5 mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">
                    Forma de pago a copiar desde la oferta {{ $this->oferta->codigo }}
                </h3>

                @if($this->oferta->formaPago->isEmpty())
                <p class="text-sm text-amber-600 bg-amber-50 rounded p-3">
                    Esta oferta no tiene forma de pago registrada. No podrá completarse la verificación del
                    contrato hasta que la oferta tenga al menos una línea de forma de pago.
                </p>
                @else
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-2">#</th>
                            <th class="p-2">Tipo</th>
                            <th class="p-2">Servicio</th>
                            <th class="p-2">Valor</th>
                            <th class="p-2 text-right">Monto calculado</th>
                            <th class="p-2">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->oferta->formaPago as $linea)
                        <tr class="border-b">
                            <td class="p-2 text-gray-400">{{ $linea->orden }}</td>
                            <td class="p-2">
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
                            <td class="p-2">{{ $linea->catalogoServicio?->nombre ?? '—' }}</td>
                            <td class="p-2">
                                {{ $linea->tipo_valor === 'porcentaje' ? $linea->valor . '%' : '$' . number_format($linea->valor, 2) }}
                            </td>
                            <td class="p-2 text-right font-medium">
                                ${{ number_format($linea->montoEsperado($this->oferta), 2) }}
                            </td>
                            <td class="p-2 text-gray-500">{{ $linea->descripcion ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-right mt-3 font-bold">
                    Total asignado: ${{ number_format($this->oferta->formaPago->sum(fn($l) => $l->montoEsperado($this->oferta)), 2) }}
                    / ${{ number_format($this->oferta->monto_total, 2) }}
                </div>
                @endif
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" wire:click="cambiarExpediente" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </button>

                <button type="submit" wire:loading.attr="disabled" wire:target="save, documento_pdf"
                    class="px-5 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">Generar Contrato</span>
                    <span wire:loading wire:target="save">Procesando y Guardando...</span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>