@section('page-title', 'Historial de Contenido')
@section('page-description', ucfirst(str_replace('_', ' ', $item->seccion->area)) . ' — ' . ($item->identificador ?? "item #{$item->id}"))

<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Historial — {{ ucfirst(str_replace('_', ' ', $item->seccion->area)) }}
                @if($item->identificador) ({{ $item->identificador }}) @endif
            </h2>
            <p class="text-sm text-gray-500">
                {{ $this->versiones->count() }} versión(es) registrada(s) para este item.
            </p>
        </div>
        <a href="{{ route('cms.lista') }}" class="text-sm text-blue-600 hover:underline">← Volver al listado</a>
    </div>

    {{-- Estado actual publicado, destacado arriba --}}
    @if($item->versionPublicada)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-xs font-semibold text-blue-700 uppercase mb-1">Actualmente publicado</p>
        <p class="text-sm text-blue-900">
            Versión #{{ $item->versionPublicada->numero_version }}
            — {{ $item->versionPublicada->valor('titulo') ?? $item->versionPublicada->valor('subtitulo') ?? 'sin título' }}
        </p>
    </div>
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-500">
        Este item nunca ha tenido una versión publicada.
    </div>
    @endif

    {{-- Timeline de versiones --}}
    <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
        @foreach($this->versiones as $version)
        @php
        $esPublicada = $item->version_publicada_id === $version->id;
        $imagen = $version->archivo('imagen_principal');
        @endphp

        <div class="ml-6 relative">
            {{-- Punto del timeline --}}
            <span class="absolute -left-[31px] top-1 w-4 h-4 rounded-full border-2 border-white
                    {{ $esPublicada ? 'bg-blue-600' : $version->estadoColor() }}"></span>

            <div class="bg-white rounded-lg shadow border {{ $esPublicada ? 'border-blue-300' : 'border-gray-200' }} p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="font-semibold text-gray-800">Versión #{{ $version->numero_version }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium text-white {{ $version->estadoColor() }}">
                            {{ $version->estadoLabel() }}
                        </span>
                        @if($esPublicada)
                        <span class="ml-1 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">
                            Vigente
                        </span>
                        @endif
                    </div>

                    @if($version->auth_status === 'Pendiente')
                    @can('cms.aprobar')
                    <a href="{{ route('cms.revisar', $version) }}"
                        class="text-xs text-blue-600 hover:underline font-medium">
                        Revisar →
                    </a>
                    @endcan
                    @endif
                </div>

                {{-- Quién hizo qué y cuándo --}}
                <div class="text-xs text-gray-500 space-y-1 mb-3">
                    <p>Propuesto por <strong>{{ $version->proponente->name ?? '—' }}</strong> el {{ $version->fecha_propuesta?->format('d/m/Y H:i') }}</p>

                    @if($version->aprobado_por)
                    <p>Aprobado por <strong>{{ $version->aprobador->name ?? '—' }}</strong> el {{ $version->fecha_aprobacion?->format('d/m/Y H:i') }}</p>
                    @endif

                    @if($version->rechazado_por)
                    <p class="text-red-600">
                        Rechazado por <strong>{{ $version->rechazador->name ?? '—' }}</strong> el {{ $version->fecha_rechazo?->format('d/m/Y H:i') }}
                        @if($version->motivo_rechazo)
                        — "{{ $version->motivo_rechazo }}"
                        @endif
                    </p>
                    @endif
                </div>

                {{-- Snapshot resumido del contenido de esta versión --}}
                <div class="flex gap-4 items-start border-t border-gray-100 pt-3">
                    @if($imagen)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($imagen['path']) }}"
                        class="w-20 h-20 object-cover rounded border flex-shrink-0">
                    @endif
                    <div class="text-sm text-gray-700 space-y-1">
                        @foreach($this->campos->where('tipo_dato', '!=', 'imagen') as $campo)
                        @php $valor = $version->valor($campo->clave) ?? ($version->archivo($campo->clave) ? '📄 archivo adjunto' : null); @endphp
                        @if($valor)
                        <p><span class="text-gray-400">{{ $campo->etiqueta }}:</span> {{ \Illuminate\Support\Str::limit($valor, 80) }}</p>
                        @endif
                        @endforeach
                    </div>
                </div>

                @if($version->observaciones)
                <p class="text-xs text-gray-400 italic mt-2 border-t border-gray-100 pt-2">
                    Nota: {{ $version->observaciones }}
                </p>
                @endif

                @if($version->tx_hash)
                <p class="text-xs text-gray-300 mt-2">tx: {{ substr($version->tx_hash, 0, 16) }}...</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>