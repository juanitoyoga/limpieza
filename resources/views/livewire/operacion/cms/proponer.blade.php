@section('page-title', 'CMS — Proponer Propuesta de Contenido')
@section('page-description', 'Banners, noticias, auspiciadores y demás contenido visual de la plataforma')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Encabezado e Contexto --}}
    <div class="bg-white p-5 rounded-lg border shadow-sm flex justify-between items-center">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-gray-800">
                    Sección: {{ $seccionExistente?->area ? ucfirst(str_replace('_', ' ', $seccionExistente->area)) : '—' }}
                </h2>
                @if($itemExistente)
                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-mono font-medium">
                    {{ $itemExistente->identificador }}
                </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 mt-1">
                @if($itemExistente)
                Generando propuesta para el Slot #{{ $itemExistente->orden }} (Siguiente versión a registrar: <strong>v{{ $itemExistente->siguienteNumeroVersion() }}</strong>).
                @else
                Generando propuesta para un nuevo slot en esta sección.
                @endif
            </p>
        </div>
        <a href="{{ route('cms.lista') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-md font-medium">
            ← Volver a Lista
        </a>
    </div>

    @if($errors->has('global'))
    <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-md">
        {{ $errors->first('global') }}
    </div>
    @endif

    {{-- Formulario Dinámico de Contenido --}}
    <form wire:submit.prevent="save" class="bg-white border rounded-lg shadow-sm p-6 space-y-5">

        <div class="border-b pb-3">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Campos de la Plantilla</h3>
            <p class="text-xs text-gray-400">Completa los valores según la estructura definida para esta división visual.</p>
        </div>

        <div class="space-y-4">
            @forelse($this->campos as $campo)
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-gray-700">
                    {{ $campo->etiqueta }}
                    @if($campo->requerido)
                    <span class="text-red-500">*</span>
                    @endif
                    <span class="text-[10px] text-gray-400 font-mono">({{ $campo->clave }})</span>
                </label>

                {{-- INPUT TIPO TEXTO --}}
                @if($campo->tipo_dato === 'texto')
                <input type="text"
                    wire:model="valores.{{ $campo->clave }}"
                    class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

                {{-- INPUT TIPO TEXTO LARGO --}}
                @elseif($campo->tipo_dato === 'texto_largo')
                <textarea wire:model="valores.{{ $campo->clave }}"
                    rows="3"
                    class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>

                {{-- INPUT TIPO URL --}}
                @elseif($campo->tipo_dato === 'url')
                <input type="url"
                    wire:model="valores.{{ $campo->clave }}"
                    placeholder="https://ejemplo.com"
                    class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

                {{-- INPUT TIPO IMAGEN O PDF --}}
                @elseif(in_array($campo->tipo_dato, ['imagen', 'documento_pdf']))
                <div class="space-y-2">
                    @if(!empty($imagenesExistentes[$campo->clave]))
                    <div class="flex items-center gap-3 p-2 bg-gray-50 border rounded-md">
                        @if($campo->tipo_dato === 'imagen')
                        <img src="{{ $imagenesExistentes[$campo->clave] }}" class="h-12 w-16 object-cover rounded border">
                        @endif
                        <span class="text-[11px] text-gray-500 truncate">Archivo adjunto guardado</span>
                    </div>
                    @endif

                    <input type="file"
                        wire:model="archivosSubidos.{{ $campo->clave }}"
                        @if($campo->tipo_dato === 'imagen') accept="image/*" @else accept="application/pdf" @endif
                    class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                    @if($campo->tipo_dato === 'imagen' && $campo->imagen_ancho && $campo->imagen_alto)
                    <p class="text-[10px] text-gray-400">Dimensiones sugeridas: {{ $campo->imagen_ancho }}x{{ $campo->imagen_alto }} px (optimización WebP automática)</p>
                    @endif
                </div>
                @endif

                @error("valores.{$campo->clave}")
                <span class="text-[11px] text-red-600 font-medium">{{ $message }}</span>
                @enderror
                @error("archivosSubidos.{$campo->clave}")
                <span class="text-[11px] text-red-600 font-medium">{{ $message }}</span>
                @enderror
            </div>
            @empty
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md text-yellow-800 text-xs">
                No hay campos parametrizados definidos para esta sección.
            </div>
            @endforelse
        </div>

        <hr class="my-4">

        {{-- Observaciones de la Propuesta --}}
        <div class="space-y-1">
            <label class="block text-xs font-semibold text-gray-700">Notas u Observaciones del Cambio</label>
            <input type="text"
                wire:model="observaciones"
                placeholder="Ej: Actualización del banner por campaña de feriado."
                class="w-full text-xs border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="flex justify-end gap-3 pt-3">
            <a href="{{ route('cms.lista') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md shadow-sm flex items-center gap-2">
                <span wire:loading.remove>
                    Enviar Propuesta
                    @if($itemExistente)
                    (v{{ $itemExistente->siguienteNumeroVersion() }})
                    @endif
                </span>
                <span wire:loading>Guardando...</span>
            </button>
        </div>

    </form>

</div>