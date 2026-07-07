@section('page-title', 'Denuncias')
@section('page-description', 'Consulta de los datos detallados de la Denuncia')

<div>
    {{-- Mensaje de éxito --}}
    @if(session()->has('message'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 dark:text-green-400 mr-3"></i>
            <p class="text-green-800 dark:text-green-300 font-medium">
                {{ session('message') }}
            </p>
        </div>
    </div>
    @endif
    {{-- Mensaje de error de autorización --}}
    @if(session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 dark:text-red-400 mr-3"></i>
            <p class="text-red-800 dark:text-red-300 font-medium">
                {{ session('error') }}
            </p>
        </div>
    </div>
    @endif
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">

            {{-- Columna Izquierda: Formulario de Visualización --}}
            <div class="p-6 md:p-8">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                        <i class="fas fa-file-alt mr-2 text-blue-500"></i>
                        Información de la Denuncia Nro. {{ $denuncia->id }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Registro perteneciente al sistema LimpiaTuRincón.
                    </p>
                </div>

                <div class="space-y-6">

                    {{-- SECCIÓN 1: DATOS GENERALES --}}
                    <div class="border-b dark:border-gray-700 pb-5">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            1. Información General del Vecino
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del Vecino</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg font-medium">
                                    {{ $denuncia->vecino?->user ? $denuncia->vecino->user->first_name . ' ' . $denuncia->vecino->user->last_name : 'Anónimo / No registrado' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Denuncia</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg">
                                    {{ $denuncia->fecha_denuncia ? $denuncia->fecha_denuncia->format('d/m/Y H:i') : 'No especificada' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordenanza Afectada</label>
                            <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <span class="font-semibold text-blue-600 dark:text-blue-400">[{{ $denuncia->ordenanza332?->codigo }}]</span>
                                {{ $denuncia->ordenanza332?->descripcion }}
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción del Hecho</label>
                            <textarea rows="3" readonly class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg resize-none text-sm">{{ $denuncia->descripcion }}</textarea>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: UBICACIÓN Y EVIDENCIA --}}
                    <div class="border-b dark:border-gray-700 pb-5">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            2. Ubicación y Evidencias
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg">
                                    {{ $denuncia->barrio?->nombre ?? 'No especificado' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Evidencia</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg flex items-center">
                                    @if(strtolower($denuncia->evidencia_tipo) == 'foto' || strtolower($denuncia->evidencia_tipo) == 'imagen')
                                    <i class="fas fa-camera text-blue-500 mr-2"></i>
                                    @else
                                    <i class="fas fa-video text-purple-500 mr-2"></i>
                                    @endif
                                    {{ $denuncia->evidencia_tipo ?? 'Sin evidencia' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección Referencial</label>
                            <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                {{ $denuncia->direccion }}
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dirección GPS (Coordenadas)</label>
                            <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg font-mono text-xs flex justify-between items-center">
                                <span>{{ $denuncia->direccion_gps ?: ($denuncia->latitud . ', ' . $denuncia->longitud) }}</span>
                                @if($denuncia->latitud && $denuncia->longitud)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $denuncia->latitud }},{{ $denuncia->longitud }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-sans font-medium flex items-center">
                                    <i class="fas fa-map-marked-alt mr-1"></i> Ver Mapa
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 3: AUDITORÍA DE PROCESOS (VERIFICADO / APROBADO / RECHAZADO) --}}
                    <div>
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            3. Historial de Auditoría y Flujo de Estados
                        </h3>

                        <div class="space-y-4">
                            {{-- Bloque: Fase de Verificación --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700/70">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fase 1: Verificación en Campo</p>
                                    @if($denuncia->verificado_por_rol)
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $denuncia->verificado_por_rol }}
                                    </span>
                                    @endif
                                </div>
                                @if($denuncia->verificado_por_rol)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <p><span class="text-gray-500">Responsable:</span> <strong class="text-gray-800 dark:text-gray-200">{{ $verificado_nombre }}</strong></p>
                                    <p class="md:text-right"><span class="text-gray-500">Fecha:</span> <span class="text-gray-800 dark:text-gray-200">{{ $denuncia->verificado_at ? $denuncia->verificado_at->format('d/m/Y H:i') : '-' }}</span></p>
                                </div>
                                @else
                                <p class="text-sm text-gray-400 italic flex items-center">
                                    <i class="fas fa-clock mr-1.5 text-amber-500"></i> Pendiente de inspección técnica u ocular.
                                </p>
                                @endif
                            </div>

                            {{-- Bloque de Resolución Final: Dependiendo de si está Aprobado o Rechazado --}}
                            @if(in_array($denuncia->estado, ['aprobada', 'resuelto']))
                            <div class="p-4 bg-green-50 dark:bg-green-950/20 rounded-xl border border-green-100 dark:border-green-900/40">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-green-700 dark:text-green-400 uppercase tracking-wide">Fase 2: Resolución Concluida (Aprobada)</p>
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        {{ $denuncia->aprobado_por_rol }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <p><span class="text-green-700/80 dark:text-green-400/80">Autorizado por:</span> <strong class="text-green-900 dark:text-green-200">{{ $aprobado_nombre }}</strong></p>
                                    <p class="md:text-right"><span class="text-green-700/80 dark:text-green-400/80">Fecha:</span> <span class="text-green-900 dark:text-green-200">{{ $denuncia->aprobado_at ? $denuncia->aprobado_at->format('d/m/Y H:i') : '-' }}</span></p>
                                </div>
                                @if($denuncia->multa_calculada > 0)
                                <div class="mt-3 pt-2.5 border-t border-green-200 dark:border-green-900/60 flex justify-between text-sm items-center">
                                    <span class="font-medium text-green-800 dark:text-green-300">Multa Aplicada:</span>
                                    <span class="font-bold text-green-700 dark:text-green-400 text-base">$. {{ number_format($denuncia->multa_calculada, 2) }}</span>
                                </div>
                                @endif
                            </div>


                            @elseif($denuncia->estado === 'rechazada')
                            <div class="p-4 bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-100 dark:border-red-900/40">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wide">Fase 2: Resolución Concluida (Rechazada)</p>
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        {{ $denuncia->rechazado_por_rol }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mb-3">
                                    <p><span class="text-red-700/80 dark:text-red-400/80">Rechazado por:</span> <strong class="text-red-900 dark:text-red-200">{{ $rechazado_nombre }}</strong></p>
                                    <p class="md:text-right"><span class="text-red-700/80 dark:text-red-400/80">Fecha:</span> <span class="text-red-900 dark:text-red-200">{{ $denuncia->rechazado_at ? $denuncia->rechazado_at->format('d/m/Y H:i') : '-' }}</span></p>
                                </div>
                                <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-900/60">
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase mb-1">Motivo del Desestimación:</p>
                                    <p class="text-gray-700 dark:text-gray-300 text-sm italic">"{{ $denuncia->motivo_rechazo ?: 'No se especificó un motivo estructurado.' }}"</p>
                                </div>
                            </div>
                            @else
                            {{-- Cualquier otro estado intermedio (ej. pendiente, en proceso) --}}
                            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 rounded-xl border border-amber-100 dark:border-amber-900/40">
                                <p class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wide mb-1">Fase 2: Resolución Final</p>
                                <p class="text-sm text-amber-800 dark:text-amber-300 italic">
                                    La denuncia está bajo consideración. Estado actual: <strong class="uppercase font-sans font-bold">{{ $denuncia->estado }}</strong>.
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Botón de regreso --}}
                    <div class="pt-6 border-t dark:border-gray-700">
                        <a href="{{ route('denuncias.index') }}" class="block w-full text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Regresar al listado de denuncias
                        </a>
                    </div>

                </div>
            </div>

            {{-- Columna Derecha: Tarjeta de Estado e Información Complementaria --}}
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50 flex flex-col justify-between">
                <div>
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4 shadow-inner">
                            <i class="fas fa-bullhorn text-3xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">
                            Seguimiento de Denuncias
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm max-w-xs mx-auto">
                            Validación vecinal participativa para el cumplimiento de normativas del municipio.
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Badge Principal de Estado --}}
                        <div class="p-4 rounded-xl text-center font-bold text-lg border shadow-sm
                            @if(in_array($denuncia->estado, ['aprobada', 'resuelto'])) bg-green-100 text-green-800 border-green-300 dark:bg-green-900/40 dark:text-green-300 dark:border-green-800
                            @elseif($denuncia->estado === 'rechazada') bg-red-100 text-red-800 border-red-300 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800
                            @else bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800 @endif">
                            <span class="text-xs uppercase block text-gray-500 dark:text-gray-400 font-normal tracking-wider mb-1">Estado en Sistema</span>
                            {{-- Icono según estado --}}
                            <x-heroicon-s-{{ 
                                in_array($denuncia->estado, ['aprobada', 'resuelto'])
                                    ? 'check-badge'
                                    : ($denuncia->estado === 'rechazada'

                                        ? 'x-circle'
                                        : 'clock')
                            }} class="w-5 h-5 mr-2" />

                            {{-- Estado en mayúsculas --}}
                            {{ strtoupper($denuncia->estado) }}

                        </div>

                        {{-- ⚡ BOTONES DE ACCIÓN FLUIDOS (Protegidos por Rol) --}}

                        @if(in_array(auth()->user()->role_name, ['Funcionario', 'Supervisor']))
                        <div class="mt-4 space-y-2">
                            @if($denuncia->estado === 'pendiente')
                            <button wire:click="$set('mostrarModalVerificar', true)" class="w-full flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                <i class="fas fa-clipboard-check mr-2"></i> Verificar en Campo
                            </button>
                            @endif

                            @if($denuncia->estado === 'verificada')
                            <div class="grid grid-cols-2 gap-2">
                                @if(auth()->user()->role_name === 'Supervisor')
                                <button wire:click="$set('mostrarModalAprobar', true)" class="w-full flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                    <i class="fas fa-thumbs-up mr-1.5"></i> Aprobar
                                </button>
                                @endif
                                <button wire:click="$set('mostrarModalRechazar', true)" class="w-full flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                    <i class="fas fa-thumbs-down mr-1.5"></i> Rechazar
                                </button>
                            </div>
                            @endif

                            @if($denuncia->estado === 'pendiente')
                            <button wire:click="$set('mostrarModalRechazar', true)" class="w-full flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm mt-2">
                                <i class="fas fa-thumbs-down mr-1.5"></i> Rechazar
                            </button>
                            @endif
                        </div>

                        @else
                        {{-- Mensaje informativo para usuarios sin permisos (ej: Dirigentes o Vecinos) --}}
                        <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-700/60 rounded-xl text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-lock mr-1"></i> Tu nivel de usuario no permite efectuar modificaciones sobre esta denuncia.
                            </p>
                        </div>
                        @endif
                        {{-- Panel de Evidencia (Si existe imagen/video en la ruta) --}}
                        @if($denuncia->evidencia_path)
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2"><i class="fas fa-image mr-1"></i> Archivo adjunto cargado</p>
                            <a href="{{ $denuncia->evidencia_url }}" target="_blank" class="block group relative rounded-lg overflow-hidden border dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                                <div class="w-full h-32 flex items-center justify-center text-sm text-blue-600 dark:text-blue-400 font-medium group-hover:bg-gray-200/50 dark:group-hover:bg-gray-800/50 transition">
                                    <span><i class="fas fa-external-link-alt mr-1"></i> Ver documento de evidencia</span>
                                </div>
                            </a>
                        </div>
                        @endif

                        {{-- Información de Seguridad / Blockchain --}}
                        @if($denuncia->verified_on_chain)
                        <div class="flex items-start p-3 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-900/30">
                            <div class="mt-0.5 text-teal-600 dark:text-teal-400"><i class="fas fa-shield-check text-base"></i></div>
                            <div class="ml-3">
                                <p class="text-xs font-bold text-teal-900 dark:text-teal-300 uppercase">Veracidad en Blockchain</p>
                                <p class="text-xs text-teal-700 dark:text-teal-400 mt-0.5 font-mono truncate max-w-[220px]">
                                    Hash: {{ $denuncia->file_hash }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Contador Global Inferior --}}
                <div class="mt-8 pt-6 border-t dark:border-gray-700">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400 font-medium">
                            <i class="fas fa-database mr-1"></i> Total denuncias del sistema
                        </span>
                        <span class="px-2.5 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full font-bold">
                            {{ App\Models\Denuncia::count() }}
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- =========================================================================
         MODALES DE ACCIÓN (Añadir al final del archivo, antes del último </div>)
         ========================================================================= --}}

    {{-- MODAL 1: CONFIRMAR VERIFICACIÓN --}}
    @if($mostrarModalVerificar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('mostrarModalVerificar', false)"></div>
        <div class="relative w-full max-w-md mx-auto z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Verificar Denuncia?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Confirmas que se ha realizado la inspección técnica reglamentaria en el lugar de los hechos. Tu nombre y rol quedarán registrados.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('mostrarModalVerificar', false)" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="verificarDenuncia" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                        Sí, Verificar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- MODAL 2: APROBAR DENUNCIA CON DESGLOSE MATRICIAL AUTOMÁTICO --}}
    @if($mostrarModalAprobar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('mostrarModalAprobar', false)"></div>
        <div class="relative w-full max-w-lg mx-auto z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">

                <div class="mb-4 flex items-center">
                    <div class="flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-3">
                        <i class="fas fa-calculator text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Liquidación de Sanción</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Verificación matricial e impactos de distribución económica</p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    Al ratificar la aprobación, el sistema cerrará el caso de la denuncia e indexará una nueva orden de cobro coactivo bajo los siguientes parámetros automatizados:
                </p>

                {{-- Tabla resumen analítica --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/60 mb-5 space-y-2.5">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Salario Mínimo Vigente (USD):</span>
                        <span class="font-mono font-medium text-gray-700 dark:text-gray-300">${{ number_format($salario_vigente, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Gravedad / Porcentaje Penalizable:</span>
                        <span class="font-mono font-medium text-gray-700 dark:text-gray-300">{{ number_format($porcentaje_infraccion, 2) }}%</span>
                    </div>

                    <div class="pt-2 border-t border-dashed border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-800 dark:text-white">Valor Liquidado de la Multa:</span>
                        <span class="text-lg font-black text-green-600 dark:text-green-400 font-mono">
                            $. {{ number_format($multa_calculada, 2) }}
                        </span>
                    </div>
                </div>

                <div class="mb-6">
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-2">Proyección estimativa de co-distribución</span>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 bg-blue-50/50 dark:bg-blue-950/10 rounded-lg border border-blue-100/60 dark:border-blue-900/30">
                            <span class="text-[10px] block font-medium text-blue-500">Fondo Barrio</span>
                            <span class="text-xs font-bold font-mono text-blue-700 dark:text-blue-300">$. {{ number_format($multa_calculada * ($pBarrio / 100), 2) }}</span>
                        </div>
                        <div class="p-2 bg-purple-50/50 dark:bg-purple-950/10 rounded-lg border border-purple-100/60 dark:border-purple-900/30">
                            <span class="text-[10px] block font-medium text-purple-500">Municipio</span>
                            <span class="text-xs font-bold font-mono text-purple-700 dark:text-purple-300">$. {{ number_format($multa_calculada * ($pMunicipio / 100), 2) }}</span>
                        </div>
                        <div class="p-2 bg-amber-50/50 dark:bg-amber-950/10 rounded-lg border border-amber-100/60 dark:border-amber-900/30">
                            <span class="text-[10px] block font-medium text-amber-500">Plataforma LTR</span>
                            <span class="text-xs font-bold font-mono text-amber-700 dark:text-amber-300">$. {{ number_format($multa_calculada * ($pPlataforma / 100), 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('mostrarModalAprobar', false)" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                        Abortar
                    </button>
                    <button type="button" wire:click="aprobarDenuncia" class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition shadow-md font-semibold">
                        Emitir y Firmar Multa
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif


    {{-- MODAL 3: RECHAZAR DENUNCIA (REQUIERE MOTIVO) --}}
    @if($mostrarModalRechazar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('mostrarModalRechazar', false)"></div>
        <div class="relative w-full max-w-md mx-auto z-50 p-4">
            <form wire:submit.prevent="rechazarDenuncia" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="mb-4 flex items-center">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mr-3">
                        <i class="fas fa-thumbs-down"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Desestimar / Rechazar Denuncia</h3>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo de Rechazo</label>
                    <textarea rows="3" wire:model.defer="motivo_rechazo" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-red-500 focus:border-red-500 resize-none" placeholder="Escriba detalladamente la justificación legal o técnica del rechazo..."></textarea>
                    @error('motivo_rechazo') <span class="text-xs text-red-500 mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('mostrarModalRechazar', false)" class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                        Cancelar
                    </button>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                        Rechazar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>