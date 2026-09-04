{{-- resources/views/components/estado-bloqueado.blade.php --}}
@props([
'titulo' => 'Acción no permitida',
'mensaje',
'detalles' => [],
'rutaRegreso',
'textoBoton' => 'Regresar',
'icono' => 'fa-ban',
'cerrarPestana' => false,
])

<div class="min-h-[50vh] flex items-center justify-center px-4">
    <div class="max-w-lg w-full text-center bg-white border border-gray-200 rounded-xl shadow-sm p-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
            <i class="fas {{ $icono }} text-2xl text-red-600"></i>
        </div>

        <h2 class="text-lg font-bold text-gray-800 mb-2">{{ $titulo }}</h2>
        <p class="text-sm text-gray-600 mb-6">{{ $mensaje }}</p>

        @if(!empty($detalles))
        <div class="text-left bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Detalle del caso</p>
            <dl class="space-y-2">
                @foreach($detalles as $etiqueta => $valor)
                <div class="flex justify-between text-sm gap-4">
                    <dt class="text-gray-500">{{ $etiqueta }}</dt>
                    <dd class="text-gray-800 font-medium text-right">{{ $valor ?? '—' }}</dd>
                </div>
                @endforeach
            </dl>
        </div>
        @endif

        @if($cerrarPestana)
        {{-- Este caso se usa cuando la vista se abrió en target="_blank"
             (ej. "Ver Documento"): navegar de vuelta duplicaría la pestaña
             con la página original. Intenta cerrar la pestaña primero;
             si el navegador lo bloquea (solo permite cerrar pestañas
             abiertas por script), cae de vuelta a navegación normal. --}}
        <a href="{{ $rutaRegreso }}"
            onclick="event.preventDefault(); window.close(); setTimeout(function(){ window.location.href = '{{ $rutaRegreso }}'; }, 300);"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-medium transition">
            <i class="fas fa-times mr-2"></i> {{ $textoBoton }}
        </a>
        @else
        <a href="{{ $rutaRegreso }}"
            class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm font-medium transition">
            <i class="fas fa-arrow-left mr-2"></i> {{ $textoBoton }}
        </a>
        @endif
    </div>
</div>