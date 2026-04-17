@section('page-title', 'Nueva Nominación')
@section('page-description', 'Registro formal y carga de expediente digital')

<div class="max-w-7xl mx-auto px-4">
    {{-- Banner Modo Inicialización --}}
    @if($isFirstTime)
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-xl shadow-sm flex items-center">
            <i class="fas fa-rocket text-amber-600 mr-3 text-xl"></i>
            <div>
                <h4 class="text-amber-800 dark:text-amber-300 font-bold text-sm uppercase italic">Modo de Inicialización Activo</h4>
                <p class="text-amber-700 dark:text-amber-400 text-xs">Se registrará al <strong>Primer Funcionario</strong>. Su perfil transicionará a Supervisor automáticamente.</p>
            </div>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        
        {{-- COLUMNA PRINCIPAL: FORMULARIO --}}
        <div class="w-full lg:w-3/4">
            <form wire:submit.prevent="save" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <div class="p-6 md:p-8 space-y-8">
                    
                    {{-- SECCIÓN 1: IDENTIFICACIÓN --}}
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 border-b border-gray-100 dark:border-gray-700 pb-2">
                            <i class="fas fa-id-card text-blue-500"></i>
                            <h3 class="font-bold text-gray-800 dark:text-white uppercase text-[10px] tracking-widest">Identificación y Rol</h3>
                        </div>
                        
                        {{-- Fila 1: Candidato y Rol --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Candidato Nominado *</label>
                                <select wire:model="candidate_user_id" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 text-sm py-2.5">
                                    <option value="">Seleccione usuario...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->last_name }} {{ $user->first_name }}</option>
                                    @endforeach
                                </select>
                                @error('candidate_user_id') <span class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Rol Asignado *</label>
                                <select wire:model.live="role_name" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 text-sm py-2.5">
                                    <option value="">Seleccione un rol...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_name') <span class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Fila 2: Institución y Origen (ALINEADOS) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-8 flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Institución / Dependencia *</label>
                                <select wire:model="released_by" @disabled(empty($instituciones)) class="w-full rounded-xl border-gray-200 dark:bg-gray-700 disabled:bg-gray-50 dark:disabled:bg-gray-900 text-sm py-2.5">
                                    <option value="">Seleccione la entidad emisora...</option>
                                    @foreach($instituciones as $inst)
                                        <option value="{{ $inst['nombre'] }}">{{ $inst['nombre'] }}</option>
                                    @endforeach
                                </select>
                                @error('released_by') <span class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Origen *</label>
                                <select wire:model="issuer_type" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 dark:border-gray-600 focus:ring-blue-500 text-sm py-2.5">
                                    <option value="DMQ">DMQ (Quito)</option>
                                    <option value="JUNTA_PARROQUIAL">Junta Parroquial</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: VIGENCIAS Y DOCUMENTO --}}
                    <div class="space-y-6 pt-4">
                        <div class="flex items-center space-x-3 border-b border-gray-100 dark:border-gray-700 pb-2">
                            <i class="fas fa-calendar-alt text-blue-500"></i>
                            <h3 class="font-bold text-gray-800 dark:text-white uppercase text-[10px] tracking-widest">Vigencia y Expediente</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Fecha Emisión</label>
                                <input type="date" wire:model="fecha_emision" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 text-sm py-2.5">
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Inicio Vigencia</label>
                                <input type="date" wire:model="fecha_inicio_vigencia" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 text-sm py-2.5">
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Fin Vigencia</label>
                                <input type="date" wire:model="fecha_fin_vigencia" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 text-sm py-2.5">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Expediente PDF *</label>
                                <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all h-[120px] flex flex-col items-center justify-center">
                                    <input type="file" wire:model="pdf" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <i class="fas fa-file-pdf text-3xl mb-2 {{ $pdf ? 'text-green-500' : 'text-blue-400' }}"></i>
                                    <p class="text-[10px] font-bold uppercase {{ $pdf ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $pdf ? $pdf->getClientOriginalName() : 'Subir expediente' }}
                                    </p>
                                </div>
                                @error('pdf') <span class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex flex-col">
                                <label class="text-[10px] font-black text-gray-400 uppercase mb-2">Observaciones</label>
                                <textarea wire:model="observaciones" class="w-full rounded-xl border-gray-200 dark:bg-gray-700 text-sm h-[120px] resize-none" placeholder="Notas administrativas..." cols="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER ACCIÓN --}}
                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg transition-all flex items-center">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i> REGISTRAR</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> PROCESANDO</span>
                    </button>
                </div>
            </form>
        </div>

 {{-- COLUMNA LATERAL: INFORMACIÓN (1/4) --}}
<div class="w-full lg:w-1/4 space-y-6">
    
    {{-- BLOQUE DINÁMICO: GUÍA DE PRIMERA VEZ --}}
    @if($isFirstTime)
        <div class="bg-gradient-to-b from-amber-50 to-white dark:from-amber-900/20 dark:to-gray-800 p-6 rounded-2xl border-2 border-amber-200 dark:border-amber-800 shadow-sm">
            <h3 class="font-bold text-amber-800 dark:text-amber-400 uppercase text-[10px] tracking-widest mb-4 flex items-center">
                <i class="fas fa-magic mr-2"></i> Flujo de Inicialización
            </h3>
            
            <div class="space-y-4">
                <div class="relative pl-6 border-l-2 border-amber-200 dark:border-amber-700 space-y-4">
                    {{-- Paso 1 --}}
                    <div class="relative">
                        <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-amber-500 border-2 border-white dark:border-gray-800"></span>
                        <h4 class="text-[10px] font-black uppercase text-amber-700 dark:text-amber-500">1. Registro Base</h4>
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 leading-tight">Se crea la nominación y se carga el expediente digital con firma de integridad.</p>
                    </div>
                    {{-- Paso 2 --}}
                    <div class="relative">
                        <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-amber-500 border-2 border-white dark:border-gray-800"></span>
                        <h4 class="text-[10px] font-black uppercase text-amber-700 dark:text-amber-500">2. Auto-Aprobación</h4>
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 leading-tight">Al ser el primer registro, el sistema omite la verificación manual y aprueba el perfil instantáneamente.</p>
                    </div>
                    {{-- Paso 3 --}}
                    <div class="relative">
                        <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-amber-500 border-2 border-white dark:border-gray-800"></span>
                        <h4 class="text-[10px] font-black uppercase text-amber-700 dark:text-amber-500">3. Ejecución de Roles</h4>
                        <p class="text-[11px] text-gray-600 dark:text-gray-400 leading-tight">Su usuario escala a <strong>Supervisor</strong> y el candidato se activa como <strong>Funcionario</strong>.</p>
                    </div>
                </div>
                
                <div class="mt-4 p-2 bg-amber-100/50 dark:bg-amber-800/20 rounded-lg text-center">
                    <p class="text-[9px] font-bold text-amber-800 dark:text-amber-400 italic">
                        * Esta guía desaparecerá tras completar el primer registro.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Panel de Información General (Siempre visible) --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="font-bold text-gray-800 dark:text-white uppercase text-[10px] tracking-widest mb-6 border-b pb-2 flex items-center">
            <i class="fas fa-info-circle text-blue-500 mr-2"></i> Resumen del Trámite
        </h3>
        
        <div class="space-y-6">
            <div class="flex items-start">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 text-blue-500">
                    <i class="fas fa-fingerprint text-sm"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-[10px] font-black uppercase text-gray-400">Hash de Seguridad</h4>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 leading-snug">Garantiza que el PDF no sea modificado tras el registro.</p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0 text-green-500">
                    <i class="fas fa-calendar-check text-sm"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-[10px] font-black uppercase text-gray-400">Vigencia Real</h4>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 leading-snug">El sistema desactivará los accesos automáticamente al vencer la fecha fin.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bloque de Soporte --}}
    <div class="bg-slate-800 p-6 rounded-2xl shadow-lg text-white">
        <h4 class="font-bold text-xs mb-3 uppercase tracking-widest text-blue-300">Auditoría Permanente</h4>
        <p class="text-[10px] leading-relaxed opacity-70 italic">
            "Toda nominación es una declaración de responsabilidad administrativa. Los datos registrados son inmutables."
        </p>
    </div>
</div>
    </div>
</div>