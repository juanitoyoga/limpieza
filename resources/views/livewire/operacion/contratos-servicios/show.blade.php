@section('page-title', 'Contrato ' . $contrato->codigo)
@section('page-description', 'Detalle del contrato de servicio')

<div class="space-y-4">

    @if (session('message'))
    <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif
    @if (session('error'))
    <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif
    @error('global')
    <div class="p-3 bg-red-100 text-red-800 rounded">{{ $message }}</div>
    @enderror

    {{-- Encabezado --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-bold">{{ $contrato->codigo }} — {{ $contrato->titulo }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $contrato->proveedor->nombre_comercial ?? '—' }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white {{ $contrato->estadoColor() }}">
                {{ $contrato->estadoLabel() }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-400">Fecha inicio</p>
                <p class="font-medium">{{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400">Fecha fin estimada</p>
                <p class="font-medium">{{ $contrato->fecha_fin_estimada?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-400">Monto total</p>
                <p class="font-medium">${{ number_format($contrato->monto_total, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-400">Oferta origen</p>
                <p class="font-medium">{{ $contrato->oferta->codigo ?? '—' }}</p>
            </div>
        </div>

        <div class="mt-4 flex gap-3">
            @if($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_PENDIENTE)
            <a href="{{ route('contratos-servicios.verificar', $contrato) }}"
                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded">Verificar</a>
            @elseif($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_VERIFICADA)
            <a href="{{ route('contratos-servicios.aprobar', $contrato) }}"
                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded">Aprobar</a>
            @endif

            @if(in_array($contrato->auth_status, [\App\Models\ContratoServicio::ESTADO_PENDIENTE, \App\Models\ContratoServicio::ESTADO_VERIFICADA]))
            <a href="{{ route('contratos-servicios.rechazar', $contrato) }}"
                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded">Rechazar</a>
            @endif

            @if($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_APROBADA)

            <a href="{{ route('contratos-servicios.liquidar', $contrato) }}"
                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">Liquidar</a>

            <a href="{{ route('contratos-servicios.rescindir', $contrato) }}"
                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded">Rescindir</a>
            @endif
        </div>
    </div>
    {{-- Documentos del contrato --}}
    <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
        <h3 class="text-lg font-bold mb-4">Documentos</h3>

        <div class="space-y-3">
            {{-- Documento original --}}
            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-file-contract text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-blue-900">Contrato Original</p>
                        <p class="text-xs text-blue-700">Documento firmado al generar el contrato</p>
                    </div>
                </div>
                @if($contrato->documento_original_path)
                <a href="{{ route('ver.documento', ['disco' => 'contratos_servicios', 'path' => base64_encode($contrato->documento_original_path)]) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-md">
                    <i class="fas fa-external-link-alt mr-2"></i> ABRIR PDF
                </a>
                @else
                <span class="text-xs text-gray-400 italic">No disponible</span>
                @endif
            </div>

            {{-- Documento de rescisión — solo visible si el contrato fue rescindido --}}
            @if($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_RESCINDIDO)
            <div class="flex items-center justify-between p-4 bg-orange-50 rounded-xl border border-orange-100">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-file-circle-xmark text-orange-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-orange-900">Acta de Rescisión</p>
                        <p class="text-xs text-orange-700">
                            Rescindido por {{ $contrato->rescindidor->last_name ?? '—' }} el {{ $contrato->fecha_rescision?->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                @if($contrato->documento_rescision_path)
                <a href="{{ route('ver.documento', ['disco' => 'contratos_servicios', 'path' => base64_encode($contrato->documento_rescision_path)]) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-lg shadow-md">
                    <i class="fas fa-external-link-alt mr-2"></i> ABRIR PDF
                </a>
                @endif
            </div>
            @endif

            {{-- Documento de liquidación — solo visible si el contrato fue liquidado --}}
            @if($contrato->auth_status === \App\Models\ContratoServicio::ESTADO_LIQUIDADO)
            <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl border border-green-100">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white rounded-lg shadow-sm">
                        <i class="fas fa-file-circle-check text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-green-900">Acta de Liquidación</p>
                        <p class="text-xs text-green-700">
                            Liquidado por {{ $contrato->liquidador->last_name ?? '—' }} el {{ $contrato->fecha_liquidacion?->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
                @if($contrato->documento_liquidacion_path)
                <a href="{{ route('ver.documento', ['disco' => 'contratos_servicios', 'path' => base64_encode($contrato->documento_liquidacion_path)]) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg shadow-md">
                    <i class="fas fa-external-link-alt mr-2"></i> ABRIR PDF
                </a>
                @endif
            </div>
            @endif
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
                    @if($contrato->puedeEditarServicios())
                    <th class="p-2 text-right">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($detalles as $detalle)
                <tr class="border-t">
                    <td class="p-2">{{ $detalle->catalogoServicio->nombre ?? $detalle->catalogo_servicio_id }}</td>
                    <td class="p-2 text-right">{{ $detalle->cantidad }}</td>
                    <td class="p-2 text-right">${{ number_format($detalle->costo_unitario, 2) }}</td>
                    <td class="p-2 text-right">${{ number_format($detalle->subtotal, 2) }}</td>
                    @if($contrato->puedeEditarServicios())
                    <td class="p-2 text-right">
                        <button wire:click="openEditDetalle({{ $detalle->id }})" class="text-blue-500 hover:text-blue-800 mr-2">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $detalle->id }})" class="text-red-500 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                    @endif
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
                    <th class="p-2 text-right">Subtotal</th>

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
    {{-- Modal editar detalle --}}
    @if($showDetalleModal)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full mx-4 p-6">
            <h3 class="font-bold text-lg mb-4">Editar servicio</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Servicio del catálogo</label>
                    <select wire:model="detalle_catalogo_servicio_id" class="w-full border px-3 py-2 rounded">
                        <option value="">Seleccione...</option>
                        @foreach(\App\Models\CatalogoServicio::orderBy('nombre')->get() as $cs)
                        <option value="{{ $cs->id }}">{{ $cs->nombre }}</option>
                        @endforeach
                    </select>
                    @error('detalle_catalogo_servicio_id') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Cantidad</label>
                    <input type="number" wire:model="detalle_cantidad" min="1" class="w-full border px-3 py-2 rounded">
                    @error('detalle_cantidad') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Costo unitario</label>
                    <input type="number" step="0.01" wire:model="detalle_costo_unitario" min="0" class="w-full border px-3 py-2 rounded">
                    @error('detalle_costo_unitario') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="$set('showDetalleModal', false)" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                <button wire:click="saveDetalle" class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Confirmación de borrado --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-sm w-full mx-4 p-6 text-center">
            <i class="fas fa-triangle-exclamation text-3xl text-red-500 mb-3"></i>
            <p class="text-gray-700 mb-4">¿Eliminar este servicio del contrato?</p>
            <div class="flex justify-center gap-3">
                <button wire:click="$set('confirmingDelete', false)" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded">Eliminar</button>
            </div>
        </div>
    </div>
    @endif
</div>