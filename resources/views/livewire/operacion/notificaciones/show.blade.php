@section('page-title', 'Notificaciones')
@section('page-description', 'Consulta de los datos detallados de la Notificación')

<div>
    @if(session()->has('message'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 dark:text-green-400 mr-3"></i>
            <p class="text-green-800 dark:text-green-300 font-medium">{{ session('message') }}</p>
        </div>
    </div>
    @endif
    @if(session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 dark:text-red-400 mr-3"></i>
            <p class="text-red-800 dark:text-red-300 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">

            {{-- Columna Izquierda --}}
            <div class="p-6 md:p-8">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                        <i class="fas fa-bell mr-2 text-blue-500"></i>
                        Notificación Nro. {{ $notificacion->id }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Justificación asociada a la
                        <a href="{{ route('denuncias.show', $notificacion->denuncia_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            Denuncia Nro. {{ $notificacion->denuncia_id }}
                        </a>.
                    </p>
                </div>

                <div class="space-y-6">

                    {{-- SECCIÓN 1: CONTRIBUYENTE Y PREDIO --}}
                    <div class="border-b dark:border-gray-700 pb-5">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            1. Datos del Contribuyente y Predio
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contribuyente</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg font-medium">
                                    {{ $notificacion->contribuyente_nombre ?: 'No especificado' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Identificación</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg">
                                    {{ $notificacion->contribuyente_identificacion ?: 'No especificado' }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de Predio</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg">
                                    {{ $notificacion->numero_predio ?: 'No especificado' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Notificación</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg">
                                    {{ $notificacion->fecha_notificacion ? $notificacion->fecha_notificacion->format('d/m/Y H:i') : 'No especificada' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ordenanza Afectada</label>
                            <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <span class="font-semibold text-blue-600 dark:text-blue-400">[{{ $notificacion->ordenanza332?->codigo }}]</span>
                                {{ $notificacion->ordenanza332?->descripcion }}
                            </div>
                        </div>

                        @if($notificacion->barrioAtributo)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plazo de Justificación</label>
                            <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                {{ $notificacion->barrioAtributo->plazo_horas }} horas · Vence:
                                <strong>{{ $notificacion->fecha_vencimiento?->format('d/m/Y H:i') ?? '—' }}</strong>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- SECCIÓN 2: UBICACIÓN Y EVIDENCIA --}}
                    <div class="border-b dark:border-gray-700 pb-5">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            2. Ubicación, Evidencia y Usuario que Justificó
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg">
                                    {{ $notificacion->barrio?->nombre ?? 'No especificado' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Evidencia</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg flex items-center">
                                    @if(strtolower($notificacion->evidencia_tipo ?? '') === 'foto')
                                    <i class="fas fa-camera text-blue-500 mr-2"></i>
                                    @elseif($notificacion->evidencia_tipo)
                                    <i class="fas fa-video text-purple-500 mr-2"></i>
                                    @endif
                                    {{ $notificacion->evidencia_tipo ?? 'Sin evidencia' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuario que Presentó la Evidencia</label>
                            <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                {{ $notificacion->user ? $notificacion->user->first_name . ' ' . $notificacion->user->last_name : 'Aún no reclamada por ningún usuario' }}
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Coordenadas de la Evidencia</label>
                            <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg font-mono text-xs flex justify-between items-center">
                                <span>{{ $notificacion->latitud }}, {{ $notificacion->longitud }}</span>
                                @if($notificacion->latitud && $notificacion->longitud)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $notificacion->latitud }},{{ $notificacion->longitud }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-sans font-medium flex items-center">
                                    <i class="fas fa-map-marked-alt mr-1"></i> Ver Mapa
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 3: AUDITORÍA DE PROCESOS --}}
                    <div>
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            3. Historial de Auditoría y Flujo de Estados
                        </h3>

                        <div class="space-y-4">
                            {{-- Fase 1: Verificación --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700/70">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fase 1: Verificación</p>
                                    @if($notificacion->verificado_por_rol)
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $notificacion->verificado_por_rol }}
                                    </span>
                                    @endif
                                </div>
                                @if($notificacion->verificado_por_rol)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <p><span class="text-gray-500">Responsable:</span> <strong class="text-gray-800 dark:text-gray-200">{{ $verificado_nombre }}</strong></p>
                                    <p class="md:text-right"><span class="text-gray-500">Fecha:</span> <span class="text-gray-800 dark:text-gray-200">{{ $notificacion->verificado_at ? $notificacion->verificado_at->format('d/m/Y H:i') : '-' }}</span></p>
                                </div>
                                @else
                                <p class="text-sm text-gray-400 italic flex items-center">
                                    <i class="fas fa-clock mr-1.5 text-amber-500"></i> Pendiente de verificación de la evidencia.
                                </p>
                                @endif
                            </div>

                            {{-- Fase 2: Resolución --}}
                            @if($notificacion->estado === \App\Models\Notificacion::ESTADO_APROBADA)
                            <div class="p-4 bg-green-50 dark:bg-green-950/20 rounded-xl border border-green-100 dark:border-green-900/40">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-green-700 dark:text-green-400 uppercase tracking-wide">Fase 2: Resolución Concluida (Aprobada)</p>
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        {{ $notificacion->aprobado_por_rol }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <p><span class="text-green-700/80 dark:text-green-400/80">Autorizado por:</span> <strong class="text-green-900 dark:text-green-200">{{ $aprobado_nombre }}</strong></p>
                                    <p class="md:text-right"><span class="text-green-700/80 dark:text-green-400/80">Fecha:</span> <span class="text-green-900 dark:text-green-200">{{ $notificacion->aprobado_at ? $notificacion->aprobado_at->format('d/m/Y H:i') : '-' }}</span></p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-green-200 dark:border-green-900/60 text-sm text-green-800 dark:text-green-300">
                                    Justificación aceptada. La denuncia asociada quedó <strong>cerrada</strong> sin multa.
                                </div>
                            </div>

                            @elseif($notificacion->estado === \App\Models\Notificacion::ESTADO_RECHAZADA)
                            <div class="p-4 bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-100 dark:border-red-900/40">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wide">Fase 2: Resolución Concluida (Rechazada)</p>
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        {{ $notificacion->rechazado_por_rol }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mb-3">
                                    <p><span class="text-red-700/80 dark:text-red-400/80">Rechazado por:</span> <strong class="text-red-900 dark:text-red-200">{{ $rechazado_nombre }}</strong></p>
                                    <p class="md:text-right"><span class="text-red-700/80 dark:text-red-400/80">Fecha:</span> <span class="text-red-900 dark:text-red-200">{{ $notificacion->rechazado_at ? $notificacion->rechazado_at->format('d/m/Y H:i') : '-' }}</span></p>
                                </div>
                                <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-900/60">
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase mb-1">Motivo del Rechazo:</p>
                                    <p class="text-gray-700 dark:text-gray-300 text-sm italic">"{{ $notificacion->motivo_rechazo ?: 'No se especificó un motivo estructurado.' }}"</p>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-red-200 dark:border-red-900/60 text-sm text-red-800 dark:text-red-300">
                                    La denuncia asociada volvió a estado <strong>pendiente</strong>.
                                </div>
                            </div>

                            @elseif($notificacion->estado === \App\Models\Notificacion::ESTADO_VENCIDA)
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-950/20 rounded-xl border border-yellow-100 dark:border-yellow-900/40">
                                <p class="text-xs font-bold text-yellow-700 dark:text-yellow-400 uppercase tracking-wide mb-1">Fase 2: Plazo Vencido</p>
                                <p class="text-sm text-yellow-800 dark:text-yellow-300 italic">
                                    El contribuyente no presentó evidencia dentro del plazo. La denuncia volvió a estado pendiente.
                                </p>
                            </div>

                            @else
                            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 rounded-xl border border-amber-100 dark:border-amber-900/40">
                                <p class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wide mb-1">Fase 2: Resolución Final</p>
                                <p class="text-sm text-amber-800 dark:text-amber-300 italic">
                                    La notificación está bajo consideración. Estado actual: <strong class="uppercase font-sans font-bold">{{ $notificacion->estado }}</strong>.
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6 border-t dark:border-gray-700">
                        <a href="{{ route('notificaciones.index') }}" class="block w-full text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Regresar al listado de notificaciones
                        </a>
                    </div>

                </div>
            </div>

            {{-- Columna Derecha: Estado y Acciones --}}
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50 flex flex-col justify-between">
                <div>
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4 shadow-inner">
                            <i class="fas fa-bell text-3xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                            {{ Str::lower($notificacion->estado) === 'pendiente' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ Str::lower($notificacion->estado) === 'enviada' ? 'bg-indigo-100 text-indigo-800' : '' }}
                            {{ Str::lower($notificacion->estado) === 'verificada' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ Str::lower($notificacion->estado) === 'aprobada' ? 'bg-green-100 text-green-800' : '' }}
                            {{ Str::lower($notificacion->estado) === 'rechazada' ? 'bg-red-100 text-red-800' : '' }}
                            {{ Str::lower($notificacion->estado) === 'vencida' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ Str::lower($notificacion->estado) === 'cerrada' ? 'bg-slate-200 text-slate-800' : '' }}">
                            {{ $notificacion->estado }}
                        </span>
                    </div>

                    @if(in_array(auth()->user()->role_name, ['Funcionario', 'Supervisor']))
                    <div class="mt-4 space-y-2">
                        @if($notificacion->estado === \App\Models\Notificacion::ESTADO_ENVIADA)
                        <div class="grid grid-cols-2 gap-2">
                            @if(auth()->user()->role_name === 'Funcionario')
                            <button wire:click="$set('mostrarModalVerificar', true)" class="w-full flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                <i class="fas fa-clipboard-check mr-1.5"></i> Verificar
                            </button>
                            @endif
                            <button wire:click="$set('mostrarModalRechazar', true)" class="w-full flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                <i class="fas fa-thumbs-down mr-1.5"></i> Rechazar
                            </button>
                        </div>
                        @endif

                        @if($notificacion->estado === \App\Models\Notificacion::ESTADO_VERIFICADA)
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
                    </div>
                    @else
                    <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-700/60 rounded-xl text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-lock mr-1"></i> Tu nivel de usuario no permite efectuar modificaciones sobre esta notificación.
                        </p>
                    </div>
                    @endif

                    {{-- Evidencia adjunta --}}
                    @if($notificacion->evidencia_path)
                    <div class="mt-4 p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2"><i class="fas fa-image mr-1"></i> Archivo adjunto cargado</p>
                        <a href="{{ $notificacion->evidencia_url }}" target="_blank" class="block group relative rounded-lg overflow-hidden border dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                            <div class="w-full h-32 flex items-center justify-center text-sm text-blue-600 dark:text-blue-400 font-medium group-hover:bg-gray-200/50 dark:group-hover:bg-gray-800/50 transition">
                                <span><i class="fas fa-external-link-alt mr-1"></i> Ver documento de evidencia</span>
                            </div>
                        </a>
                    </div>
                    @endif

                    {{-- Blockchain --}}
                    @if($notificacion->verified_on_chain)
                    <div class="mt-4 flex items-start p-3 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-900/30">
                        <div class="mt-0.5 text-teal-600 dark:text-teal-400"><i class="fas fa-shield-check text-base"></i></div>
                        <div class="ml-3">
                            <p class="text-xs font-bold text-teal-900 dark:text-teal-300 uppercase">Veracidad en Blockchain</p>
                            <p class="text-xs text-teal-700 dark:text-teal-400 mt-0.5 font-mono truncate max-w-[220px]">
                                Hash: {{ $notificacion->file_hash }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t dark:border-gray-700">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400 font-medium">
                            <i class="fas fa-database mr-1"></i> Total notificaciones del sistema
                        </span>
                        <span class="px-2.5 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full font-bold">
                            {{ App\Models\Notificacion::count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 1: VERIFICAR --}}
    @if($mostrarModalVerificar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('mostrarModalVerificar', false)"></div>
        <div class="relative w-full max-w-md mx-auto z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Verificar Notificación?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Confirmas que la evidencia presentada corresponde efectivamente al lugar de la denuncia. Tu nombre y rol quedarán registrados.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('mostrarModalVerificar', false)" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="verificarNotificacion" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                        Sí, Verificar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 2: APROBAR --}}
    @if($mostrarModalAprobar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('mostrarModalAprobar', false)"></div>
        <div class="relative w-full max-w-lg mx-auto z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="mb-4 flex items-center">
                    <div class="flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mr-3">
                        <i class="fas fa-check-double text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aprobar Justificación</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">La denuncia asociada quedará cerrada sin multa</p>
                    </div>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
                    Al aprobar, el contribuyente queda exento de sanción por esta infracción y la
                    <strong>Denuncia Nro. {{ $notificacion->denuncia_id }}</strong> se cerrará automáticamente.
                    Esta acción quedará registrada en blockchain.
                </p>

                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('mostrarModalAprobar', false)" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="aprobarNotificacion" class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition shadow-md font-semibold">
                        Aprobar y Cerrar Denuncia
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 3: RECHAZAR --}}
    @if($mostrarModalRechazar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('mostrarModalRechazar', false)"></div>
        <div class="relative w-full max-w-md mx-auto z-50 p-4">
            <form wire:submit.prevent="rechazarNotificacion" class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="mb-4 flex items-center">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mr-3">
                        <i class="fas fa-thumbs-down"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Rechazar Justificación</h3>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    La <strong>Denuncia Nro. {{ $notificacion->denuncia_id }}</strong> volverá a estado pendiente para continuar su proceso sancionatorio normal.
                </p>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo de Rechazo</label>
                    <textarea rows="3" wire:model.defer="motivo_rechazo" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-red-500 focus:border-red-500 resize-none" placeholder="Explica por qué la evidencia no justifica la infracción..."></textarea>
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