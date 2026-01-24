@section('page-title', 'Aprobación de Nominación')
@section('page-description', 'Firma de aprobación final y activación de credenciales')

<div>
    {{-- Feedback de Errores de Seguridad --}}
    @error('security')
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-md flex items-center">
            <i class="fas fa-shield-alt mr-3 text-xl"></i>
            <span class="font-bold">{{ $message }}</span>
        </div>
    @enderror

    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 shadow-sm flex items-center">
            <i class="fas fa-exclamation-triangle mr-3"></i>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-3">
            
            {{-- Columna Izquierda: Información (2/3) --}}
            <div class="lg:col-span-2 p-6 md:p-8 space-y-8">
                <div class="flex items-center justify-between border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-check-double text-indigo-500 mr-2"></i>
                        Revisión Final para Aprobación
                    </h2>
                    <div class="text-right">
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase tracking-wider">
                            Estado: Verificada
                        </span>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">Registrado el: {{ $details['fechas']['registro'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Datos del Candidato y Rol --}}
                    <div class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Candidato Nominado</label>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $details['candidate_name'] }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">Rol a Asignar</label>
                            <p class="text-md font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 inline-block px-3 py-1 rounded-lg italic">
                                <i class="fas fa-user-tag mr-1 text-xs"></i> {{ $details['role_name'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Datos de Verificación --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/40 rounded-xl border border-gray-200 dark:border-gray-600 relative overflow-hidden">
                        <div class="absolute right-0 top-0 opacity-10 p-2">
                            <i class="fas fa-user-check text-4xl"></i>
                        </div>
                        <label class="text-xs font-bold text-gray-500 uppercase block mb-3 border-b pb-2">Información de Verificación</label>
                        <div class="space-y-2">
                            <p class="text-sm"><strong>Responsable:</strong> {{ $details['verifier_name'] }}</p>
                            <p class="text-sm"><strong>Fecha:</strong> {{ $details['fechas']['verificacion'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- NUEVA SECCIÓN: Vigencia y Cronología --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-slate-50 dark:bg-gray-700/20 rounded-xl border border-slate-200 dark:border-gray-600">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block">Fecha Emisión</label>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300"><i class="far fa-calendar-check mr-2 text-indigo-400"></i>{{ $details['fechas']['emision'] }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block">Inicio Vigencia</label>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300"><i class="far fa-play-circle mr-2 text-green-400"></i>{{ $details['fechas']['inicio'] }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase block">Fin Vigencia</label>
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300"><i class="far fa-stop-circle mr-2 text-red-400"></i>{{ $details['fechas']['fin'] }}</p>
                    </div>
                </div>

                {{-- Observaciones Previas --}}
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 rounded-r-xl shadow-sm">
                    <label class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase flex items-center">
                        <i class="fas fa-comment-dots mr-2"></i> Observaciones Previas
                    </label>
                    <p class="text-sm italic text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        {{ $details['obs_previa'] ?: 'Sin observaciones previas registradas en el flujo.' }}
                    </p>
                </div>

                {{-- Formulario de Aprobación --}}
                <form wire:submit.prevent="save" class="pt-4 space-y-6">

                    @if($isFirstTimeMode)
                        <div class="p-5 bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-200 dark:border-amber-800 rounded-xl flex items-start space-x-4 animate-pulse">
                            <i class="fas fa-tools text-amber-600 text-2xl mt-1"></i>
                            <div>
                                <h4 class="text-amber-800 dark:text-amber-300 font-bold text-sm uppercase italic">Modo Inicialización Activo</h4>
                                <p class="text-amber-700 dark:text-amber-400 text-xs mt-1">Está registrando al <strong>Primer Auditor</strong>. Su perfil transicionará a SuperAdmin automáticamente.</p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Dictamen de Aprobación *</label>
                        <textarea wire:model="observaciones" rows="3" 
                            class="w-full px-4 py-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                            placeholder="Escriba el fundamento legal o administrativo de esta aprobación..."></textarea>
                        @error('observaciones') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg border border-indigo-100 dark:border-indigo-800">
                        <label class="flex items-start cursor-pointer group">
                            <input type="checkbox" wire:model="acepta_responsabilidad" class="mt-1 w-5 h-5 text-indigo-600 rounded cursor-pointer">
                            <span class="ml-3 text-sm text-indigo-900 dark:text-indigo-200 group-hover:text-indigo-700 transition-colors">
                                Certifico la validez de los datos y <strong>autorizo</strong> la activación inmediata de credenciales.
                            </span>
                        </label>
                        @error('acepta_responsabilidad') <p class="text-red-500 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>
            {{-- Columna Derecha: Respaldo y Ayuda --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 md:p-8 border-t lg:border-t-0 flex flex-col space-y-8">
                <div>
                    <h3 class="font-bold text-gray-800 dark:text-white uppercase text-xs tracking-widest mb-4">Expediente Digital</h3>
                    <a href="{{ route('ver.documento', ['path' => base64_encode($details['pdf'])]) }}" target="_blank"
                        class="group block p-6 bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 hover:border-indigo-400 transition-all text-center">
                        <i class="fas fa-file-pdf text-5xl text-red-500 mb-4 group-hover:scale-110 transition-transform"></i>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-200">Visualizar PDF Original</p>
                        <p class="text-[10px] text-gray-400 mt-2 uppercase">Hash de integridad verificado</p>
                    </a>
                </div>

                <div class="space-y-4">
                    <h3 class="font-bold text-gray-800 dark:text-white uppercase text-xs tracking-widest">Seguridad</h3>
                    <div class="flex items-start text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 p-3 rounded-lg">
                        <i class="fas fa-history text-indigo-500 mr-3 mt-1"></i>
                        <p>Esta acción creará un registro en la tabla de <strong>{{ $details['role_name'] }}s</strong> y se notificará al usuario.</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center h-screen">
                <button type="submit" wire:loading.attr="disabled"
                    class="w-1/2 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg transition-all flex items-center justify-center transform active:scale-[0.98]">
                    
                    <span wire:loading.remove>
                        <i class="fas fa-file-signature mr-2"></i> EJECUTAR APROBACIÓN FINAL
                    </span>
                    
                    <span wire:loading>
                        <i class="fas fa-circle-notch fa-spin mr-2"></i> REGISTRANDO EN SISTEMA...
                    </span>
                </button>
            </div>
            
                </form>
            </div>


        </div>
    </div>
</div>