@section('page-title', 'Verificar Resolución')
@section('page-description', 'Verificación de la resolución barrial')

<div>
    @if(session()->has('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @error('global')
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-800 font-medium">{{ $message }}</p>
        </div>
    </div>
    @enderror

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">

            {{-- FORMULARIO --}}
            <div class="p-6 md:p-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-signature mr-2 text-blue-500"></i>
                    Datos de la Resolución
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Código</label>
                        <input type="text" value="{{ $resolucion->codigo }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-500">Tipo</label>
                            <input type="text" value="{{ $resolucion->tipo }}" readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-500">Barrio</label>
                            <input type="text" value="{{ $resolucion->barrio->nombre ?? '—' }}" readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Título</label>
                        <input type="text" value="{{ $resolucion->titulo }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                    </div>

                    @if($resolucion->descripcion)
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Descripción</label>
                        <textarea readonly rows="3"
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">{{ $resolucion->descripcion }}</textarea>
                    </div>
                    @endif

                    {{-- Documento PDF --}}
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white rounded-lg shadow-sm">
                                    <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-900">Documento de Respaldo</p>
                                    <p class="text-xs text-blue-700">Verifique la validez legal del archivo</p>
                                </div>
                            </div>
                            @if($resolucion->documento_original_path)
                            <a href="{{ route('ver.documento', ['disco' => 'resoluciones', 'path' => base64_encode($resolucion->documento_original_path)]) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-md">
                                <i class="fas fa-external-link-alt mr-2"></i> ABRIR PDF
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Participantes / Firmantes --}}
                    @if($resolucion->participantes->isNotEmpty())
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Participantes de la reunión barrial</label>
                        <ul class="space-y-1 text-sm text-gray-700">
                            @foreach($resolucion->participantes as $participante)
                            <li class="flex justify-between border-b border-gray-100 py-1">
                                <span>{{ $participante->nombre_firmante }} @if($participante->cargo)<span class="text-gray-400">— {{ $participante->cargo }}</span>@endif</span>
                                <span class="text-gray-400 text-xs">Orden {{ $participante->orden_firma }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <hr class="border-gray-200">

                    <div>
                        <label class="block text-sm font-semibold mb-2">Observaciones de verificación *</label>
                        <textarea wire:model="observaciones" rows="3"
                            placeholder="Describa lo verificado sobre esta resolución..."
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                        @error('observaciones') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="check-resp" wire:model="acepta_responsabilidad"
                                class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded cursor-pointer">
                            <label for="check-resp" class="text-sm text-yellow-900 cursor-pointer">
                                <strong>Declaración Jurada:</strong> Como Dirigente barrial, declaro que he verificado la información de esta resolución y asumo la responsabilidad de este acto.
                            </label>
                        </div>
                        @error('acepta_responsabilidad') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex justify-center space-x-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                             text-white rounded-lg font-semibold text-base shadow-md transition-all transform active:scale-95
                            flex items-center justify-center">
                            <span wire:loading.remove class="flex items-center">
                                <i class="fas fa-shield-check mr-2"></i> VERIFICAR
                            </span>
                            <span wire:loading class="flex items-center">
                                <i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESANDO...
                            </span>
                        </button>

                        <a href="{{ route('resoluciones.lista') }}"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition
                            flex items-center justify-center shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i> Regresar
                        </a>
                    </div>
                </form>
            </div>

            {{-- PANEL LATERAL --}}
            <div class="p-6 md:p-8 bg-gray-50 flex flex-col justify-center">
                <div class="max-w-xs mx-auto text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-6">
                        <i class="fas fa-fingerprint text-4xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Seguridad del Proceso</h3>
                    <ul class="text-left space-y-4 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Se generará un log de auditoría inmutable con su ID de usuario y marca de tiempo.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Tras la verificación, la resolución pasará a espera de aprobación del Presidente barrial.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>El documento PDF quedará vinculado permanentemente a esta verificación.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>