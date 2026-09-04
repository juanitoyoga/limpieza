@section('page-title', 'Revisar Propuesta de Contenido')
@section('page-description', 'Comparación entre lo publicado y lo propuesto')

@if($bloqueado)
<x-estado-bloqueado :titulo="$bloqueadoTitulo" :mensaje="$bloqueadoMensaje" :ruta-regreso="$bloqueadoRuta" />
@else

<form wire:submit.prevent="procesarAccion" class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                {{ ucfirst(str_replace('_', ' ', $version->item->seccion->area ?? '')) }}
                @if($version->item->identificador)
                — {{ $version->item->identificador }}
                @else
                — item #{{ $version->item->id }}
                @endif
            </h2>
            <p class="text-sm text-gray-500">
                Propuesto por {{ $version->proponente->name ?? '—' }}
                el {{ $version->fecha_propuesta?->format('d/m/Y H:i') }}
                — Versión #{{ $version->numero_version }}
            </p>
        </div>

        <a href="{{ route('cms.lista') }}" class="text-sm text-blue-600 hover:underline">← Volver al listado</a>
    </div>

    {{-- Error global --}}
    @error('global')
    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">{{ $message }}</div>
    @enderror

    {{-- Nota del proponente --}}
    @if($version->observaciones)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
        <strong>Nota del proponente:</strong> {{ $version->observaciones }}
    </div>
    @endif

    {{-- Comparación lado a lado --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Columna: Actualmente publicado --}}
        <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">
                Actualmente publicado
                @if($this->versionVigente)
                (v{{ $this->versionVigente->numero_version }})
                @endif
            </h3>

            @if(! $this->versionVigente)
            <p class="text-sm text-gray-400 italic">Este item nunca ha tenido una versión publicada.</p>
            @else
            @foreach($this->campos as $campo)
            <div class="mb-4 pb-4 border-b border-gray-100 last:border-0">
                <p class="text-xs text-gray-500 mb-1">{{ $campo->etiqueta }}</p>

                @if($campo->tipo_dato === 'imagen' || $campo->tipo_dato === 'documento_pdf')
                @php $archivo = $this->versionVigente->archivo($campo->clave); @endphp

                @if($archivo)
                @if($campo->tipo_dato === 'imagen')
                <img src="{{ Storage::disk('public')->url($archivo['path']) }}" class="max-h-40 rounded border">
                @else
                <a href="{{ Storage::disk('public')->url($archivo['path']) }}" target="_blank" class="text-blue-600 text-sm hover:underline">📄 Ver PDF actual</a>
                @endif
                @else
                <p class="text-sm text-gray-300">— vacío —</p>
                @endif

                @elseif($campo->tipo_dato === 'url')
                @php $valor = $this->versionVigente->valor($campo->clave); @endphp

                @if($valor)
                <a href="{{ $valor }}" target="_blank" class="text-blue-600 text-sm hover:underline break-all">{{ $valor }}</a>
                @else
                <p class="text-sm text-gray-300">— vacío —</p>
                @endif

                @else
                <p class="text-sm text-gray-700 whitespace-pre-line">
                    {{ $this->versionVigente->valor($campo->clave) ?: '— vacío —' }}
                </p>
                @endif
            </div>
            @endforeach
            @endif
        </div>

        {{-- Columna: Propuesta nueva --}}
        <div class="bg-white rounded-lg shadow border-2 border-blue-300 p-5">
            <h3 class="text-sm font-semibold text-blue-600 uppercase mb-4">
                Propuesta nueva (v{{ $version->numero_version }})
            </h3>

            @foreach($this->campos as $campo)
            @php
            $valorNuevo = $version->valor($campo->clave);
            $archivoNuevo = $version->archivo($campo->clave);
            $valorAnterior = $this->versionVigente?->valor($campo->clave);

            $cambio = $campo->tipo_dato === 'imagen' || $campo->tipo_dato === 'documento_pdf'
            ? ($archivoNuevo['hash'] ?? null) !== ($this->versionVigente?->archivo($campo->clave)['hash'] ?? null)
            : $valorNuevo !== $valorAnterior;
            @endphp

            <div class="mb-4 pb-4 border-b border-gray-100 last:border-0 {{ $cambio ? 'bg-yellow-50 -mx-2 px-2 rounded' : '' }}">
                <p class="text-xs text-gray-500 mb-1">
                    {{ $campo->etiqueta }}
                    @if($cambio)
                    <span class="text-amber-600 font-medium">· modificado</span>
                    @endif
                </p>

                @if($campo->tipo_dato === 'imagen' || $campo->tipo_dato === 'documento_pdf')
                @if($archivoNuevo)
                @if($campo->tipo_dato === 'imagen')
                <img src="{{ Storage::disk('public')->url($archivoNuevo['path']) }}" class="max-h-40 rounded border">
                @else
                <a href="{{ Storage::disk('public')->url($archivoNuevo['path']) }}" target="_blank" class="text-blue-600 text-sm hover:underline">📄 Ver PDF propuesto</a>
                @endif
                @else
                <p class="text-sm text-gray-300">— vacío —</p>
                @endif

                @elseif($campo->tipo_dato === 'url')
                @if($valorNuevo)
                <a href="{{ $valorNuevo }}" target="_blank" class="text-blue-600 text-sm hover:underline break-all">{{ $valorNuevo }}</a>
                @else
                <p class="text-sm text-gray-300">— vacío —</p>
                @endif

                @else
                <p class="text-sm text-gray-800 whitespace-pre-line font-medium">
                    {{ $valorNuevo ?: '— vacío —' }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Panel de acciones dentro del formulario --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Aprobar --}}
            <div>
                <h4 class="text-sm font-semibold text-green-700 mb-2">Aprobar y publicar</h4>
                <p class="text-xs text-gray-500 mb-3">
                    Esta versión reemplazará de inmediato el contenido visible en la plataforma para este item.
                </p>

                <button type="submit"
                    wire:click="$set('accion', 'aprobar')"
                    wire:confirm="¿Aprobar y publicar esta propuesta ahora mismo?"
                    class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow-sm">
                    Aprobar y publicar
                </button>

            </div>

            {{-- Rechazar --}}
            <div>
                <h4 class="text-sm font-semibold text-red-700 mb-2">Rechazar</h4>

                <textarea wire:model.live="motivo_rechazo"
                    rows="2"
                    placeholder="Motivo del rechazo (obligatorio)"
                    class="w-full border-gray-300 rounded-md text-sm mb-2 focus:ring-red-500 focus:border-red-500"></textarea>

                @error('motivo_rechazo')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                @enderror

                <button type="submit"
                    wire:click="$set('accion', 'rechazar')"
                    class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-md shadow-sm">
                    Rechazar propuesta
                </button>

            </div>

        </div>
    </div>

</form>

@endif