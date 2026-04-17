@section('page-title', 'Ver Nominación')
@section('page-description', 'Informacion generada de una nominación')

<div>
    {{-- Mensaje de éxito --}}
    @if(session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800 dark:text-green-300 font-medium">
                    {{ session('success') }}
                </p>
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

                    {{-- Candidato --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Candidato *</label>
                        <input type="string" 
                            wire:model="candidate_user_id" 
                            value="{{ $candidato }}" 
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                    </div>

                    {{-- Rol --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Rol *</label>
                        <input type="string" 
                            wire:model="role_name" 
                            value="{{ $role_name }}" 
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                    </div>

                    {{-- Institucion que certifica el documento --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Institución</label>
                        <input type="string" 
                            wire:model="released_by" 
                            value="{{ $released_by }}" 
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">                        

                    </div>

                    {{-- Origen --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Origen *</label>
                        <input type="string" 
                            wire:model="issuer_type" 
                            value="{{ $issuer_type}}" 
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">                        

                    </div>

                    {{-- PDF --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Documentación de Respaldo
                        </h3>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-red-100 rounded-lg">
                                    <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Archivo de Nominación</p>
                                    <p class="text-xs text-gray-500">Formato PDF</p>
                                </div>
                            </div>
                    
                            {{-- BOTÓN AZUL --}}
                            <a href="{{ route('ver.documento', ['path' => base64_encode($pdf)]) }}" 
                               target="_blank"
                               class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                ABRIR DOCUMENTO
                            </a>
                        </div>
                    </div>

                    {{-- Fecha de Emisión --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Fecha de Emisión *</label>
                        <input type="string" 
                            wire:model="fecha_emision" 
                            value="{{ $fecha_emision }}" 
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">                        

                    </div>

                    {{-- Fecha de Inicio de Funciones --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Fecha de Inicio de Funciones *</label>
                        <input type="string" 
                            wire:model="fecha_inicio_vigencia" 
                            value="{{ $fecha_inicio_vigencia }}" 
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">                         

                    </div>

                    {{-- Fecha de Terminación de Funciones --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Fecha de Terminación de Funciones *</label>
                        <input type="string" 
                            wire:model="fecha_fin_vigencia" 
                            value="{{ $fecha_fin_vigencia }}" 
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300"> 
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Observaciones</label>
                        <textarea wire:model="observaciones" rows="3" readonly
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg"></textarea>

                    </div>
                    

                    {{-- BOTÓN --}}
                    <div class="pt-6 border-t">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                    
                            <span wire:loading.remove>
                                <i class="fas fa-check-circle mr-2"></i>
                                Regresar a la lista de nominaciones
                            </span>
                    
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Procesando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

                        

            {{-- PANEL INFORMATIVO --}}
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50">
                <div class="text-center">
                    <i class="fas fa-shield-alt text-4xl text-blue-500 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Proceso Formal</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        La nominación esta registrada con un número de trámite, auditoría y hash de integridad.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ERRORES GLOBALES --}}
    @if($errors->any())
        <div class="mt-6 bg-red-50 border border-red-200 p-4 rounded-lg">
            <ul class="list-disc pl-5 text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
