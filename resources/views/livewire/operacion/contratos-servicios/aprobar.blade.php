@section('page-title', 'Aprobar Contrato')
@section('page-description', 'Aprobación del contrato de servicio')

@if($bloqueado)
<x-estado-bloqueado :titulo="$bloqueadoTitulo" :mensaje="$bloqueadoMensaje" :ruta-regreso="$bloqueadoRuta" />
@else
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">

        <div class="p-6 md:p-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-file-signature mr-2 text-blue-500"></i> Datos del Contrato
            </h2>

            @error('global')
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">{{ $message }}</div>
            @enderror

            <form wire:submit.prevent="save" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-500">Código</label>
                    <input type="text" value="{{ $contrato->codigo }}" readonly
                        class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Proveedor</label>
                        <input type="text" value="{{ $contrato->proveedor->nombre_comercial ?? '—' }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Monto total</label>
                        <input type="text" value="${{ number_format($contrato->monto_total, 2) }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                    </div>
                </div>

                {{-- Servicios del contrato --}}
                <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Servicios contratados</h3>

                        @if($contrato->puedeEditarServicios())
                        <span class="text-xs text-gray-400">Editable mientras el contrato esté Pendiente</span>
                        @else
                        <span class="text-xs text-gray-500 italic">Bloqueado — el contrato está {{ strtolower($contrato->estadoLabel()) }}</span>
                        @endif
                    </div>

                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 text-left">Servicio</th>
                                <th class="p-2 text-right">Cantidad</th>
                                <th class="p-2 text-right">Costo unitario</th>
                                <th class="p-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contrato->detalles as $detalle)
                            <tr class="border-t">
                                <td class="p-2">{{ $detalle->catalogoServicio->nombre ?? $detalle->catalogo_servicio_id }}</td>
                                <td class="p-2 text-right">{{ $detalle->cantidad }}</td>
                                <td class="p-2 text-right">${{ number_format($detalle->costo_unitario, 2) }}</td>
                                <td class="p-2 text-right">${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-400">Sin servicios registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                {{-- Nuevo: Forma de pago del contrato --}}
                <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Pagos Acordados</h3>
                    </div>
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 text-left">Tipo Pago</th>
                                <th class="p-2 text-right">Servicio</th>
                                <th class="p-2 text-right">Valor Acordado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contrato->formaPago as $linea)
                            <tr class="border-t">
                                <td class="p-2">{{ $linea->tipo }}</td>
                                <td class="p-2 text-right">
                                    {{ $linea->catalogoServicio->nombre ?? ' '}}
                                </td>
                                <td class="p-2 text-right">{{ number_format($linea->valor, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-gray-400">Sin pagos registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>


                <hr class="border-gray-200">

                <div>
                    <label class="block text-sm font-semibold mb-2">Observaciones de aprobación *</label>
                    <textarea wire:model="observaciones" rows="3"
                        placeholder="Describa lo verificado sobre este contrato..."
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    @error('observaciones') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-500">Verificado por</label>
                    <input type="text"
                        value="{{ $contrato->verificador ? $contrato->verificador->last_name . ' ' . $contrato->verificador->first_name : 'N/A' }}"
                        readonly class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                </div>
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="check-resp" wire:model="acepta_responsabilidad"
                            class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded cursor-pointer">
                        <label for="check-resp" class="text-sm text-yellow-900 cursor-pointer">
                            <strong>Declaración Jurada:</strong> Como Dirigente barrial, declaro que he verificado la información de este contrato y asumo la responsabilidad de este acto.
                        </label>
                    </div>
                    @error('acepta_responsabilidad') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex justify-center space-x-4">
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg font-semibold shadow-md flex items-center justify-center">
                        <span wire:loading.remove class="flex items-center"><i class="fas fa-shield-check mr-2"></i> APROBAR</span>
                        <span wire:loading class="flex items-center"><i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESANDO...</span>
                    </button>
                    <a href="{{ route('contratos-servicios.lista') }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i> Regresar
                    </a>
                </div>
            </form>
        </div>

        <div class="p-6 md:p-8 bg-gray-50 flex flex-col justify-center">
            <div class="max-w-xs mx-auto text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6">
                    <i class="fas fa-fingerprint text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">Seguridad del Proceso</h3>
                <ul class="text-left space-y-4 text-sm text-gray-600">
                    <li class="flex items-start"><i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Se generará un log de auditoría inmutable con su ID de usuario y marca de tiempo.</span>
                    </li>
                    <li class="flex items-start"><i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Tras la aprobación, el contrato pasará a su ejecucion.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif