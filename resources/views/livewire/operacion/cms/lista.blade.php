@section('page-title', 'Gestión de Contenido (CMS)')
@section('page-description', 'Banners, noticias, auspiciadores y demás contenido visual de la plataforma')

<div class="space-y-6">

    {{-- Pestañas por Área --}}
    <div class="border-b border-gray-200">
        <nav class="flex flex-wrap gap-2">
            @foreach($areas as $area)
            <button wire:click="seleccionarArea('{{ $area }}')"
                class="px-4 py-2 text-sm font-medium rounded-t-lg border-b-2 transition
                        {{ $areaActiva === $area ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ ucfirst(str_replace('_', ' ', $area)) }}
            </button>
            @endforeach
        </nav>
    </div>

    {{-- Filtros y Acciones --}}
    <div class="flex justify-between items-center">
        <select wire:model.live="filtroEstado" class="border-gray-300 rounded-md text-sm">
            <option value="">Todos los estados</option>
            @foreach($estados as $estado)
            <option value="{{ $estado }}">{{ $estado }}</option>
            @endforeach
        </select>

        @can('cms.proponer')
        @if($seccion && $this->esColeccion() && !$this->alcanzoMaximo())
        <a href="{{ route('cms.proponer', ['contenidoSeccionId' => $seccion->id]) }}"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
            + Nuevo Slot de {{ ucfirst($seccion->area) }}
        </a>
        @endif
        @endcan
    </div>

    {{-- Listado de Ítems (Slots) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        @php
        $publicada = $item->versionPublicada;
        $ultima = $item->ultimaVersion;
        $hayPropuestaDistinta = $ultima && (!$publicada || $ultima->id !== $publicada->id);
        @endphp

        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden flex flex-col justify-between">
            <div>
                {{-- Bloque: Versión Publicada actual --}}
                @php
                $archivoPublicado = $publicada?->archivo('imagen_principal') ?? $publicada?->primerArchivo();
                @endphp

                @if($archivoPublicado && isset($archivoPublicado['path']))
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($archivoPublicado['path']) }}"
                    alt="{{ $publicada->valor('titulo') ?? $item->identificador }}"
                    class="w-full h-40 object-cover">
                @else
                <div class="w-full h-40 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                    Sin versión publicada
                </div>
                @endif

                <div class="p-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-mono text-gray-400">{{ $item->identificador }}</span>
                        <span class="text-xs font-semibold text-gray-500">Slot #{{ $item->orden }}</span>
                    </div>

                    <h3 class="font-semibold text-gray-800 mb-2">
                        {{ $publicada?->valor('titulo') ?? $publicada?->valor('subtitulo') ?? '— Sin título publicado —' }}
                    </h3>

                    @if($publicada)
                    <span class="px-2 py-0.5 rounded text-xs font-medium text-white {{ $publicada->estadoColor() }}">
                        {{ $publicada->estadoLabel() }} (v{{ $publicada->numero_version }})
                    </span>
                    @endif

                    {{-- Bloque: Última Propuesta Registrada --}}
                    @if($hayPropuestaDistinta)
                    <div class="mt-3 pt-3 border-t border-dashed border-amber-300 bg-amber-50/50 p-2 rounded">
                        <p class="text-xs font-semibold text-amber-800 mb-2">
                            Propuesta pendiente / reciente (v{{ $ultima->numero_version }})
                        </p>

                        @php $archivoUltimo = $ultima->archivo('imagen_principal') ?? $ultima->primerArchivo(); @endphp
                        @if($archivoUltimo && isset($archivoUltimo['path']))
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($archivoUltimo['path']) }}"
                            class="w-full h-24 object-cover rounded mb-2">
                        @endif

                        <p class="text-xs font-medium text-gray-700 mb-1">
                            {{ $ultima->valor('titulo') ?? $ultima->valor('subtitulo') ?? '— Sin título —' }}
                        </p>

                        <span class="px-2 py-0.5 rounded text-xs font-medium text-white {{ $ultima->estadoColor() }}">
                            {{ $ultima->estadoLabel() }}
                        </span>

                        @if($ultima->auth_status === \App\Models\ContenidoVersion::ESTADO_PENDIENTE)
                        @can('cms.aprobar')
                        <a href="{{ route('cms.revisar', $ultima) }}"
                            class="block mt-2 text-xs text-blue-600 hover:underline font-medium">
                            → Revisar versión v{{ $ultima->numero_version }}
                        </a>
                        @endcan
                        @elseif($ultima->auth_status === \App\Models\ContenidoVersion::ESTADO_RECHAZADA && $ultima->motivo_rechazo)
                        <p class="text-xs text-red-600 mt-1">Motivo: {{ $ultima->motivo_rechazo }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Pie con acciones --}}
            <div class="p-4 border-t bg-gray-50 flex justify-between items-center text-xs">
                <a href="{{ route('cms.historial', $item) }}"
                    class="text-blue-600 hover:underline font-medium">
                    Historial (v{{ $item->versiones->count() }})
                </a>

                @can('cms.proponer')
                <a href="{{ route('cms.proponer', ['contenidoSeccionId' => $seccion->id, 'contenidoItemId' => $item->id]) }}"
                    class="px-2 py-1 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-100 font-medium">
                    Proponer Cambio
                </a>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-lg border border-dashed">
            <p class="text-sm text-gray-400 italic mb-3">
                No hay ítems registrados para el área "{{ ucfirst(str_replace('_', ' ', $areaActiva)) }}".
            </p>
            @can('cms.proponer')
            @if($seccion)
            <a href="{{ route('cms.proponer', ['contenidoSeccionId' => $seccion->id]) }}"
                class="text-sm text-blue-600 hover:underline font-medium">
                Crear el primer ítem en esta sección →
            </a>
            @endif
            @endcan
        </div>
        @endforelse
    </div>

</div>