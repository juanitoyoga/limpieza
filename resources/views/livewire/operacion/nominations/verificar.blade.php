@section('page-title', 'Verificar Nominación')
@section('page-description', 'Registro formal de la verificación a una nominación')

<div>
    {{-- Mensajes de Feedback --}}
    @if(session()->has('success'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-800 dark:text-red-300 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">

            {{-- FORMULARIO --}}
            <div class="p-6 md:p-8">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-file-signature mr-2 text-blue-500"></i>
                    Información de la Nominación
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">

                    {{-- Candidato (Lectura) --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Candidato</label>
                        <input type="text" value="{{ $details['candidate_name'] }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Rol (Lectura) --}}
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-500">Rol Nominado</label>
                            <input type="text" value="{{ $details['role_name'] }}" readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                        </div>
                        {{-- Nominador (Lectura) --}}
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-500">Nominado por</label>
                            <input type="text" value="{{ $details['nominator_name'] }}" readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                        </div>
                    </div>

                    {{-- Institución --}}
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-500">Institución / Dependencia</label>
                        <input type="text" value="{{ $details['released_by'] }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 text-gray-600">
                    </div>

                    {{-- Documento PDF --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                                    <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-900 dark:text-blue-300">Documento de Respaldo</p>
                                    <p class="text-xs text-blue-700 dark:text-blue-400">Verifique la validez legal del archivo</p>
                                </div>
                            </div>
                            <a href="{{ route('ver.documento', ['disco' => 'nominations', 'path' => base64_encode($details['pdf'])]) }}"

                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-md">
                                <i class="fas fa-external-link-alt mr-2"></i> ABRIR PDF
                            </a>
                        </div>
                    </div>

                    {{-- Fechas (Lectura) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase">Emisión</label>
                            <p class="text-sm font-medium">{{ $details['fechas']['emision'] }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase">Inicio Vigencia</label>
                            <p class="text-sm font-medium">{{ $details['fechas']['inicio'] }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase">Fin Vigencia</label>
                            <p class="text-sm font-medium">{{ $details['fechas']['fin'] }}</p>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    {{-- SECCIÓN DE ENTRADA (Lo que el usuario llena) --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2">Observaciones de Verificación *</label>
                        <textarea wire:model="observaciones" rows="3"
                            placeholder="Describa cualquier hallazgo o confirmación de los datos..."
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                        @error('observaciones') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="check-resp"
                                wire:model="acepta_responsabilidad"
                                class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded cursor-pointer">
                            <label for="check-resp" class="text-sm text-yellow-900 dark:text-yellow-200 cursor-pointer">
                                <strong>Declaración Jurada:</strong> Declaro que he verificado la información física/digital y asumo la responsabilidad administrativa y legal por esta validación.
                            </label>
                        </div>
                        @error('acepta_responsabilidad') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex justify-center space-x-4">

                        {{-- BOTÓN PRINCIPAL --}}
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                             text-white rounded-lg font-semibold text-base shadow-md transition-all transform active:scale-95
                            flex items-center justify-center">

                            <span wire:loading.remove class="flex items-center">
                                <i class="fas fa-shield-check mr-2"></i>
                                VERIFICAR
                            </span>

                            <span wire:loading class="flex items-center">
                                <i class="fas fa-circle-notch fa-spin mr-2"></i>
                                PROCESANDO...
                            </span>
                        </button>

                        {{-- BOTÓN REGRESAR --}}
                        <a href="{{ route('nominations.index') }}"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition
                            flex items-center justify-center shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Regresar
                        </a>

                    </div>


                </form>
            </div>

            {{-- PANEL INFORMATIVO LATERAL --}}
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50 flex flex-col justify-center">
                <div class="max-w-xs mx-auto text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-6">
                        <i class="fas fa-fingerprint text-4xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Seguridad del Proceso</h3>
                    <ul class="text-left space-y-4 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Se generará un log de auditoría inmutable con su ID de usuario y marca de tiempo.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span>Si es la primera configuración del sistema, se activarán automáticamente los roles de supervisión.</span>
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