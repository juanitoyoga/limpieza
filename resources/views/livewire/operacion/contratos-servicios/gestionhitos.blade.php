@section('page-title', 'Gestión de Hitos')
@section('page-description', 'Verificación y aprobación de hitos por contrato')

<div class="space-y-6">
    @if (session('message'))
    <div class="p-3 bg-green-100 text-green-800 rounded-lg text-xs font-semibold">{{ session('message') }}</div>
    @endif
    @if (session('error'))
    <div class="p-3 bg-red-100 text-red-800 rounded-lg text-xs font-semibold">{{ session('error') }}</div>
    @endif
    @error('global')
    <div class="p-3 bg-red-100 text-red-800 rounded-lg text-xs font-semibold">{{ $message }}</div>
    @enderror

    <!-- PANEL DE CONTRATOS VIGENTES -->
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Contratos Vigentes</h2>
                <p class="text-xs text-gray-500">Seleccione un contrato para gestionar los hitos de sus servicios</p>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar contrato..."
                class="px-4 py-2 border rounded-lg text-sm w-full md:w-64 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($contratosVigentes as $item)
            <div wire:click="seleccionarContrato({{ $item->id }})"
                class="p-4 rounded-xl border cursor-pointer transition-all duration-200 {{ $contratoId === $item->id ? 'border-blue-500 bg-blue-50/50 shadow-md' : 'border-gray-200 hover:border-gray-300 bg-white' }}">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-mono text-xs font-bold text-blue-600">{{ $item->codigo }}</span>
                    <span class="px-2 py-0.5 text-xs rounded-full text-white {{ $item->estadoColor() }}">
                        {{ $item->estadoLabel() }}
                    </span>
                </div>
                <h3 class="text-sm font-semibold text-gray-800 line-clamp-1">{{ $item->titulo }}</h3>
                <p class="text-xs text-gray-500 mt-1">${{ number_format($item->monto_total, 2) }}</p>
            </div>
            @empty
            <div class="col-span-full py-6 text-center text-gray-400 text-sm">No hay contratos vigentes disponibles.</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $contratosVigentes->links() }}</div>
    </div>

    <!-- SERVICIOS DEL CONTRATO SELECCIONADO -->
    @if($this->contratoSeleccionado)
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-6">
        <div class="flex justify-between items-center border-b pb-4">
            <div>
                <h3 class="text-md font-bold text-gray-800">Servicios del Contrato: {{ $this->contratoSeleccionado->codigo }}</h3>
                <p class="text-xs text-gray-500">{{ $this->contratoSeleccionado->titulo }}</p>
            </div>
            <button wire:click="limpiarSeleccion" class="text-sm text-gray-500 hover:text-gray-700">Cerrar</button>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach($this->contratoSeleccionado->detalles as $detalle)
            @php
            $hito = $detalle->hito;
            $tieneEvidenciasCompletas = $detalle->ejecucionCompleta();
            $estaVerificado = $hito?->estaVerificado();
            $estaAprobado = $hito?->estaAprobado();
            $estaRechazado = $hito?->estado === \App\Models\HitoContratoServicio::ESTADO_RECHAZADO;
            @endphp

            <div class="py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-sm text-gray-800">{{ $detalle->catalogoServicio->nombre ?? 'Servicio #' . $detalle->catalogo_servicio_id }}</span>

                        @if($hito)
                        <span class="px-2 py-0.5 text-xs rounded font-medium 
                                        {{ $estaAprobado ? 'bg-green-100 text-green-700' : ($estaRechazado ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800') }}">
                            Hito {{ ucfirst($hito->estado) }}
                        </span>
                        @elseif($tieneEvidenciasCompletas)
                        <span class="px-2 py-0.5 text-xs rounded bg-blue-100 text-blue-700 font-medium">Listo para Verificar</span>
                        @else
                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-500">Sin Evidencias Completas</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500">Cantidad: {{ $detalle->cantidad }} | Subtotal: ${{ number_format($detalle->subtotal, 2) }}</p>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="flex items-center gap-2">
                    @if(!$hito && $tieneEvidenciasCompletas)
                    @if($esDirigente)
                    <button wire:click="abrirModal({{ $detalle->id }}, 'verificar')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-shield-alt"></i> Verificar
                    </button>
                    @else
                    <button wire:click="abrirModal({{ $detalle->id }}, 'revisar')" class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-semibold">
                        <i class="fas fa-eye"></i> Ver Evidencias
                    </button>
                    @endif
                    @elseif($hito && $estaVerificado && !$estaAprobado && !$estaRechazado)
                    @if($esPresidente)
                    <button wire:click="abrirModal({{ $detalle->id }}, 'aprobar')" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-check"></i> Aprobar
                    </button>
                    <button wire:click="abrirModal({{ $detalle->id }}, 'rechazar')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                    @else
                    <button wire:click="abrirModal({{ $detalle->id }}, 'revisar')" class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-semibold">
                        <i class="fas fa-eye"></i> Revisar Hito
                    </button>
                    @endif
                    @elseif($hito && ($estaAprobado || $estaRechazado))
                    <button wire:click="abrirModal({{ $detalle->id }}, 'revisar')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold flex items-center gap-1">
                        <i class="fas fa-search"></i> Ver Detalles
                    </button>
                    @else
                    <span class="text-xs text-gray-400 italic">Incompleto</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- MODAL DE PROCESAMIENTO / REVISIÓN DETALLADA -->
    @if($showActionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full overflow-hidden border border-gray-200">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-sm capitalize">
                    {{ $actionType === 'verificar' ? 'Verificar Hito (Dirigente)' : ($actionType === 'aprobar' ? 'Aprobar Hito (Presidente)' : ($actionType === 'rechazar' ? 'Rechazar Hito (Presidente)' : 'Detalles de Hito y Evidencias')) }}
                </h3>
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>

            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                @error('global')
                <div class="p-3 bg-red-50 text-red-700 text-xs rounded-lg border border-red-200">{{ $message }}</div>
                @enderror

                <!-- 1. TRAZABILIDAD E INFORMACIÓN EXTENDIDA DEL HITO -->
                @if($this->hitoSeleccionado)
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 font-semibold uppercase block text-[10px]">Estado Actual</span>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded font-bold uppercase text-[10px]
                                    {{ $this->hitoSeleccionado->estado === 'aprobado' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $this->hitoSeleccionado->estado === 'verificado' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $this->hitoSeleccionado->estado === 'rechazado' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $this->hitoSeleccionado->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ $this->hitoSeleccionado->estado }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-400 font-semibold uppercase block text-[10px]">Hash Evidencias (Blockchain)</span>
                        <p class="font-mono text-[11px] text-gray-700 truncate mt-0.5" title="{{ $this->hitoSeleccionado->hash_evidencias ?? 'Pendiente' }}">
                            {{ $this->hitoSeleccionado->hash_evidencias ?? 'Sin generar' }}
                        </p>
                    </div>

                    <div>
                        <span class="text-gray-400 font-semibold uppercase block text-[10px]">Verificado por</span>
                        <p class="text-gray-800 font-medium">
                            {{ $this->hitoSeleccionado->verificadoPor?->name ?? 'N/A' }}
                            @if($this->hitoSeleccionado->verificado_at)
                            <span class="text-[10px] text-gray-400">({{ $this->hitoSeleccionado->verificado_at->format('d/m/Y H:i') }})</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-gray-400 font-semibold uppercase block text-[10px]">Aprobación / Rechazo</span>
                        <p class="text-gray-800 font-medium">
                            @if($this->hitoSeleccionado->aprobadoPor)
                            <span class="text-green-700">Aprobado por {{ $this->hitoSeleccionado->aprobadoPor->name }}</span>
                            <span class="text-[10px] text-gray-400">({{ $this->hitoSeleccionado->aprobado_at?->format('d/m/Y H:i') }})</span>
                            @elseif($this->hitoSeleccionado->rechazadoPor)
                            <span class="text-red-700">Rechazado por {{ $this->hitoSeleccionado->rechazadoPor->name }}</span>
                            <span class="text-[10px] text-gray-400">({{ $this->hitoSeleccionado->rechazado_at?->format('d/m/Y H:i') }})</span>
                            @else
                            <span class="text-gray-400">Pendiente</span>
                            @endif
                        </p>
                    </div>

                    @if($this->hitoSeleccionado->descripcion_servicio)
                    <div class="col-span-full border-t border-gray-200 pt-2">
                        <span class="text-gray-400 font-semibold uppercase block text-[10px]">Observaciones Registradas</span>
                        <p class="text-gray-700 italic mt-0.5">{{ $this->hitoSeleccionado->descripcion_servicio }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- 2. GALERÍA MULTIMEDIA ANTES / DESPUÉS -->
                @php
                $evidencias = $this->hitoSeleccionado?->evidencias
                ?? \App\Models\EvidenciaHito::where('contrato_servicio_detalle_id', $this->detalleSeleccionadoId)->get();
                @endphp

                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-gray-700">Evidencias Multimedia Capturadas en Campo</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- BLOQUE ANTES -->
                        <div class="p-3 border rounded-xl bg-gray-50/50 space-y-3">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded uppercase">Evidencias ANTES</span>
                            <div class="space-y-3">
                                @forelse($evidencias->where('tipo', 'ANTES') as $ev)
                                <div class="bg-white p-2 rounded-lg border border-gray-200 space-y-1">
                                    @if($ev->formato === 'FOTO')
                                    <a href="{{ Storage::url($ev->ruta_archivo) }}" target="_blank" class="block relative group">
                                        <img src="{{ Storage::url($ev->ruta_archivo) }}" class="w-full h-36 object-cover rounded-md group-hover:opacity-90 transition">
                                        <span class="absolute bottom-1 right-1 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded">Ver Pantalla Completa</span>
                                    </a>
                                    @else
                                    <video controls class="w-full h-36 object-cover rounded-md">
                                        <source src="{{ Storage::url($ev->ruta_archivo) }}">
                                    </video>
                                    @endif
                                    <p class="text-[11px] text-gray-600 truncate">{{ $ev->descripcion ?? 'Sin descripción' }}</p>
                                    <div class="flex justify-between items-center text-[9px] text-gray-400">
                                        <span>{{ $ev->capturadoPor?->name ?? 'App Móvil' }}</span>
                                        <span>{{ $ev->capturado_en_campo_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="py-8 text-center text-xs text-gray-400">Sin evidencias fotográficas previas</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- BLOQUE DESPUÉS -->
                        <div class="p-3 border rounded-xl bg-gray-50/50 space-y-3">
                            <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] font-bold rounded uppercase">Evidencias DESPUÉS</span>
                            <div class="space-y-3">
                                @forelse($evidencias->where('tipo', 'DESPUES') as $ev)
                                <div class="bg-white p-2 rounded-lg border border-gray-200 space-y-1">
                                    @if($ev->formato === 'FOTO')
                                    <a href="{{ Storage::url($ev->ruta_archivo) }}" target="_blank" class="block relative group">
                                        <img src="{{ Storage::url($ev->ruta_archivo) }}" class="w-full h-36 object-cover rounded-md group-hover:opacity-90 transition">
                                        <span class="absolute bottom-1 right-1 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded">Ver Pantalla Completa</span>
                                    </a>
                                    @else
                                    <video controls class="w-full h-36 object-cover rounded-md">
                                        <source src="{{ Storage::url($ev->ruta_archivo) }}">
                                    </video>
                                    @endif
                                    <p class="text-[11px] text-gray-600 truncate">{{ $ev->descripcion ?? 'Sin descripción' }}</p>
                                    <div class="flex justify-between items-center text-[9px] text-gray-400">
                                        <span>{{ $ev->capturadoPor?->name ?? 'App Móvil' }}</span>
                                        <span>{{ $ev->capturado_en_campo_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="py-8 text-center text-xs text-gray-400">Sin evidencias fotográficas posteriores</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORMULARIO DE ACCIÓN -->
                @if(in_array($actionType, ['verificar', 'aprobar', 'rechazar']))
                <div class="space-y-3 border-t border-gray-200 pt-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Observaciones / Justificación *</label>
                        <textarea wire:model="observaciones" rows="3" class="w-full p-2.5 border rounded-lg text-xs focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Describa el resultado de la revisión..."></textarea>
                        @error('observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-start gap-2 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <input type="checkbox" id="acepta" wire:model="acepta_responsabilidad" class="mt-0.5 rounded border-gray-300 text-blue-600">
                        <label for="acepta" class="text-xs text-yellow-900 cursor-pointer">
                            Declaro que he revisado las evidencias del servicio y asumo la responsabilidad de este acto como <strong>{{ Auth::user()->role_name }}</strong>.
                        </label>
                    </div>
                    @error('acepta_responsabilidad') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            <!-- FOOTER CON BOTONES -->
            <div class="p-4 bg-gray-50 border-t flex justify-end gap-2">
                <button wire:click="cerrarModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-lg">
                    {{ $actionType === 'revisar' ? 'Cerrar' : 'Cancelar' }}
                </button>

                @if($actionType === 'verificar')
                <button wire:click="procesarAccion" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg flex items-center gap-1">
                    <span wire:loading.remove><i class="fas fa-shield-alt"></i> Confirmar Verificación</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                </button>
                @elseif($actionType === 'aprobar')
                <button wire:click="procesarAccion" wire:loading.attr="disabled" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg flex items-center gap-1">
                    <span wire:loading.remove><i class="fas fa-check"></i> Confirmar Aprobación</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                </button>
                @elseif($actionType === 'rechazar')
                <button wire:click="procesarAccion" wire:loading.attr="disabled" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg flex items-center gap-1">
                    <span wire:loading.remove><i class="fas fa-times"></i> Confirmar Rechazo</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin"></i> Procesando...</span>
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>