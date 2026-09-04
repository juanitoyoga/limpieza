{{-- livewire/operacion/log-sistema/lista.blade.php --}}
<div class="bg-white p-6 rounded-xl shadow border border-gray-200">

    @if (session('message'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    @if(!empty($seleccionados))
    <div class="mb-4 flex items-center justify-between bg-red-50 border border-red-200 rounded-lg px-4 py-2">
        <span class="text-sm text-red-800">{{ count($seleccionados) }} seleccionado(s)</span>
        <button wire:click="confirmarBorradoMasivo"
            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm rounded">
            <i class="fas fa-trash mr-1"></i> Eliminar seleccionados
        </button>
    </div>
    @endif

    <table class="w-full text-left border-collapse text-sm">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="p-3 w-8">
                    <input type="checkbox" wire:model.live="seleccionarTodos">
                </th>
                <th class="p-3 cursor-pointer" wire:click="sortBy('created_at')">
                    Fecha @if($sortField==='created_at') {{ $sortDirection==='asc'?'↑':'↓' }} @endif
                </th>
                <th class="p-3">Nivel</th>
                <th class="p-3">Origen</th>
                <th class="p-3">Tipo</th>
                <th class="p-3">Comentario</th>
                <th class="p-3">Usuario</th>
                <th class="p-3 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3">
                    <input type="checkbox" wire:model.live="seleccionados" value="{{ $log->id }}">
                </td>
                <td class="p-3 text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td class="p-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-white {{ $log->nivelColor() }}">
                        <i class="fas {{ $log->nivelIcono() }}"></i> {{ strtoupper($log->nivel) }}
                    </span>
                </td>
                <td class="p-3 text-gray-600 truncate max-w-[180px]" title="{{ $log->origen }}">{{ $log->origen }}</td>
                <td class="p-3 text-gray-500">{{ $log->tipo_origen }}</td>
                <td class="p-3 truncate max-w-[240px]">{{ $log->comentario }}</td>
                <td class="p-3 text-gray-500">{{ $log->usuario->email ?? '—' }}</td>
                <td class="p-3 text-right">
                    <button wire:click="verDetalle({{ $log->id }})" class="text-gray-500 hover:text-gray-800">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="p-6 text-center text-gray-500">Sin registros.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $logs->links() }}</div>

    {{-- Modal de detalle --}}
    @if($showDetalleModal && $detalle)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" wire:click.self="$set('showDetalleModal', false)">
        <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full mx-4 max-h-[85vh] overflow-y-auto">
            <div class="p-6 border-b flex justify-between items-start">
                <div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-white {{ $detalle->nivelColor() }} mb-2">
                        {{ strtoupper($detalle->nivel) }}
                    </span>
                    <h3 class="font-bold text-lg">{{ $detalle->comentario }}</h3>
                    <p class="text-xs text-gray-400 mt-1">{{ $detalle->created_at->format('d/m/Y H:i:s') }} — {{ $detalle->origen }} ({{ $detalle->tipo_origen }})</p>
                </div>
                <button wire:click="$set('showDetalleModal', false)" class="text-gray-400 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Detalle / Stack trace</p>
                <pre class="bg-gray-900 text-green-400 text-xs p-4 rounded-lg overflow-x-auto whitespace-pre-wrap">{{ is_array($detalle->mensajeErrorDecodificado()) ? json_encode($detalle->mensajeErrorDecodificado(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $detalle->mensajeErrorDecodificado() }}</pre>
                @if($detalle->usuario)
                <p class="text-xs text-gray-500 mt-4">Usuario: {{ $detalle->usuario->email }} — IP: {{ $detalle->ip ?? '—' }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Confirmación de borrado masivo --}}
    @if($confirmingDelete)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg max-w-sm w-full mx-4 p-6 text-center">
            <i class="fas fa-triangle-exclamation text-3xl text-red-500 mb-3"></i>
            <p class="text-gray-700 mb-4">¿Eliminar {{ count($seleccionados) }} registro(s) de forma permanente?</p>
            <div class="flex justify-center gap-3">
                <button wire:click="$set('confirmingDelete', false)" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                <button wire:click="borrarSeleccionados" class="px-4 py-2 bg-red-600 text-white rounded">Eliminar</button>
            </div>
        </div>
    </div>
    @endif
</div>