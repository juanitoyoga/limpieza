@section('page-title', 'Contratos')
@section('page-description', 'Consulta de los datos detallados del Contrato')

<div>
    {{-- Mensaje de éxito --}}
    @if(session()->has('success') || session()->has('message'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 dark:text-green-400 mr-3"></i>
            <p class="text-green-800 dark:text-green-300 font-medium">
                {{ session('success') ?? session('message') }}
            </p>
        </div>
    </div>
    @endif

    {{-- Mensaje de error --}}
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
                        <i class="fas fa-file-contract mr-2 text-blue-500"></i>
                        Información del Contrato Nro. {{ $contrato->numero_contrato ?? '—' }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Registro perteneciente al sistema LimpiaTuRincón.
                    </p>
                </div>

                <div class="space-y-6">

                    {{-- SECCIÓN 1: DATOS GENERALES --}}
                    <div class="border-b dark:border-gray-700 pb-5">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            1. Información General del Contrato
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de Contrato</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg font-medium">
                                    {{ $contrato->numero_contrato ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barrio Asignado</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg font-semibold text-blue-600 dark:text-blue-400">
                                    {{ $contrato->barrio->nombre ?? '—' }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Inicio</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                    {{ $contrato->fecha_inicio?->format('d/m/Y') ?? '—' }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Fin</label>
                                <div class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                    {{ $contrato->fecha_fin?->format('d/m/Y') ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECCIÓN 2: PARÁMETROS DE DISTRIBUCIÓN --}}
                    <div class="border-b dark:border-gray-700 pb-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                                2. Parámetros Matriciales de Distribución Eco.
                            </h3>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                <i class="fas fa-info-circle"></i> Informativo
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            Porcentajes aplicados sobre cada multa generada bajo este marco contractual.
                        </p>

                        <div class="grid grid-cols-3 gap-3 text-center mb-4">
                            <div class="p-3 bg-blue-50/50 dark:bg-blue-950/20 rounded-xl border border-blue-100/60 dark:border-blue-900/40">
                                <span class="text-xs block font-semibold text-blue-500 uppercase tracking-wide mb-1">Fondo Barrio</span>
                                <span class="text-xl font-black font-mono text-blue-700 dark:text-blue-300">{{ $contrato->porcentaje_barrio }}%</span>
                            </div>
                            <div class="p-3 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-xl border border-indigo-100/60 dark:border-indigo-900/40">
                                <span class="text-xs block font-semibold text-indigo-500 uppercase tracking-wide mb-1">Municipio (DMQ)</span>
                                <span class="text-xl font-black font-mono text-indigo-700 dark:text-indigo-300">{{ $contrato->porcentaje_dmq }}%</span>
                            </div>
                            <div class="p-3 bg-purple-50/50 dark:bg-purple-950/20 rounded-xl border border-purple-100/60 dark:border-purple-900/40">
                                <span class="text-xs block font-semibold text-purple-500 uppercase tracking-wide mb-1">Plataforma LTR</span>
                                <span class="text-xl font-black font-mono text-purple-700 dark:text-purple-300">{{ $contrato->porcentaje_ltr }}%</span>
                            </div>
                        </div>

                        {{-- Barra Proporcional --}}
                        <div class="flex rounded-full overflow-hidden h-2.5 bg-gray-100 dark:bg-gray-700 shadow-inner">
                            <div class="bg-blue-500" style="width: {{ $contrato->porcentaje_barrio }}%"></div>
                            <div class="bg-indigo-500" style="width: {{ $contrato->porcentaje_dmq }}%"></div>
                            <div class="bg-purple-500" style="width: {{ $contrato->porcentaje_ltr }}%"></div>
                        </div>
                    </div>

                    {{-- SECCIÓN 3: AUDITORÍA Y FLUJO DE ESTADOS --}}
                    <div>
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-4">
                            3. Historial de Auditoría y Flujo de Estados
                        </h3>

                        <div class="space-y-4">
                            {{-- Bloque 1: Ingreso --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700/70">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fase 1: Registro e Ingreso</p>
                                    @if($contrato->id_ingreso)
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $contrato->rol_ingreso ?? 'Operador' }}
                                    </span>
                                    @endif
                                </div>
                                @if($contrato->id_ingreso)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <p><span class="text-gray-500">Ingresado por ID:</span> <strong class="text-gray-800 dark:text-gray-200">{{ $contrato->id_ingreso }}</strong></p>
                                    <p class="md:text-right"><span class="text-gray-500">Fecha:</span> <span class="text-gray-800 dark:text-gray-200">{{ $contrato->fecha_ingreso?->format('d/m/Y H:i') }}</span></p>
                                </div>
                                @else
                                <p class="text-sm text-gray-400 italic flex items-center">
                                    <i class="fas fa-clock mr-1.5 text-amber-500"></i> Pendiente de registro inicial.
                                </p>
                                @endif
                            </div>

                            {{-- Bloque 2: Verificación --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700/70">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Fase 2: Verificación Técnica</p>
                                    @if($contrato->id_verificacion)
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        {{ $contrato->rol_verificacion ?? 'Técnico' }}
                                    </span>
                                    @endif
                                </div>
                                @if($contrato->id_verificacion)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <p><span class="text-gray-500">Verificado por ID:</span> <strong class="text-gray-800 dark:text-gray-200">{{ $contrato->id_verificacion }}</strong></p>
                                    <p class="md:text-right"><span class="text-gray-500">Fecha:</span> <span class="text-gray-800 dark:text-gray-200">{{ $contrato->fecha_verificacion?->format('d/m/Y H:i') }}</span></p>
                                </div>
                                @else
                                <p class="text-sm text-gray-400 italic flex items-center">
                                    <i class="fas fa-clock mr-1.5 text-amber-500"></i> Pendiente de validación e inspección del documento.
                                </p>
                                @endif
                            </div>

                            {{-- Bloque de Resolución Final --}}
                            @if($contrato->estado === \App\Models\Contrato::ESTADO_APROBADO)
                            <div class="p-4 bg-green-50 dark:bg-green-950/20 rounded-xl border border-green-100 dark:border-green-900/40">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-green-700 dark:text-green-400 uppercase tracking-wide">Fase 3: Resolución Concluida (Aprobado)</p>
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        {{ $contrato->rol_aprobacion ?? 'Supervisor' }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <p><span class="text-green-700/80 dark:text-green-400/80">Autorizado por ID:</span> <strong class="text-green-900 dark:text-green-200">{{ $contrato->id_aprobacion }}</strong></p>
                                    <p class="md:text-right"><span class="text-green-700/80 dark:text-green-400/80">Fecha Firma:</span> <span class="text-green-900 dark:text-green-200">{{ $contrato->fecha_aprobacion?->format('d/m/Y H:i') ?? '-' }}</span></p>
                                </div>
                            </div>

                            @elseif($contrato->estado === \App\Models\Contrato::ESTADO_RECHAZADO)
                            <div class="p-4 bg-red-50 dark:bg-red-950/20 rounded-xl border border-red-100 dark:border-red-900/40">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wide">Fase 3: Resolución Concluida (Rechazado)</p>
                                    <span class="text-xs px-2 py-0.5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300">
                                        {{ $contrato->rol_rechazo ?? 'Autoridad' }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mb-3">
                                    <p><span class="text-red-700/80 dark:text-red-400/80">Desestimado por ID:</span> <strong class="text-red-900 dark:text-red-200">{{ $contrato->id_rechazo ?? '—' }}</strong></p>
                                    <p class="md:text-right"><span class="text-red-700/80 dark:text-red-400/80">Fecha:</span> <span class="text-red-900 dark:text-red-200">{{ $contrato->fecha_rechazo?->format('d/m/Y H:i') ?? '-' }}</span></p>
                                </div>
                                <div class="p-3 bg-white dark:bg-gray-800 rounded-lg border border-red-200 dark:border-red-900/60">
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase mb-1">Motivo del Rechazo:</p>
                                    <p class="text-gray-700 dark:text-gray-300 text-sm italic">"{{ $contrato->motivo_rechazo ?: 'No se especificó un motivo estructurado.' }}"</p>
                                </div>
                            </div>
                            @else
                            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 rounded-xl border border-amber-100 dark:border-amber-900/40">
                                <p class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wide mb-1">Fase 3: Resolución Final</p>
                                <p class="text-sm text-amber-800 dark:text-amber-300 italic">
                                    El marco contractual está bajo evaluación. Estado actual: <strong class="uppercase font-sans font-bold">{{ $contrato->estadoLabel() }}</strong>.
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Botón de regreso --}}
                    <div class="pt-6 border-t dark:border-gray-700">
                        <a href="{{ route('contratos.index') }}" class="block w-full text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Regresar al listado de contratos
                        </a>
                    </div>

                </div>
            </div>

            {{-- Columna Derecha: Tarjeta de Estado e Información Complementaria --}}
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50 flex flex-col justify-between">
                <div>
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4 shadow-inner">
                            <i class="fas fa-file-signature text-3xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">
                            Seguimiento de Contratos
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm max-w-xs mx-auto">
                            Validación institucional y jurídica de convenios y acuerdos de distribución del municipio.
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Badge Principal de Estado --}}
                        <div class="p-4 rounded-xl text-center font-bold text-lg border shadow-sm
                            @if($contrato->estado === \App\Models\Contrato::ESTADO_APROBADO) bg-green-100 text-green-800 border-green-300 dark:bg-green-900/40 dark:text-green-300 dark:border-green-800
                            @elif($contrato->estado === \App\Models\Contrato::ESTADO_RECHAZADO) bg-red-100 text-red-800 border-red-300 dark:bg-red-900/40 dark:text-red-300 dark:border-red-800
                            @else bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-800 @endif">
                            <span class="text-xs uppercase block text-gray-500 dark:text-gray-400 font-normal tracking-wider mb-1">Estado en Sistema</span>

                            <div class="inline-flex items-center justify-center">
                                <x-heroicon-s-{{ 
                                    $contrato->estado === \App\Models\Contrato::ESTADO_APROBADO
                                        ? 'check-badge'
                                        : ($contrato->estado === \App\Models\Contrato::ESTADO_RECHAZADO
                                            ? 'x-circle'
                                            : 'clock')
                                }} class="w-5 h-5 mr-2" />
                                {{ strtoupper($contrato->estadoLabel()) }}
                            </div>
                        </div>

                        {{-- ⚡ BOTONES DE ACCIÓN FLUIDOS (Seguridad por ID / Estado) --}}
                        @php $authId = auth()->id(); @endphp

                        @if(in_array($contrato->estado, [\App\Models\Contrato::ESTADO_PENDIENTE, \App\Models\Contrato::ESTADO_VERIFICADO]))
                        <div class="mt-4 space-y-2">
                            {{-- Botón Verificar --}}
                            @if($contrato->estado === \App\Models\Contrato::ESTADO_PENDIENTE && $authId !== $contrato->id_ingreso)
                            <button wire:click="$set('mostrarModalVerificar', true)" class="w-full flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                <i class="fas fa-clipboard-check mr-2"></i> Validar & Verificar Contrato
                            </button>
                            @endif

                            {{-- Botones de Cierre (Aprobar/Rechazar) --}}
                            <div class="grid grid-cols-2 gap-2">
                                @if($contrato->estado === \App\Models\Contrato::ESTADO_VERIFICADO && $authId !== $contrato->id_ingreso && $authId !== $contrato->id_verificacion)
                                <button wire:click="$set('mostrarModalAprobar', true)" class="flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                    <i class="fas fa-stamp mr-1.5"></i> Aprobar
                                </button>
                                @else
                                <div></div> {{-- Spacer --}}
                                @endif

                                <button wire:click="$set('modalRechazo', true)" class="flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm">
                                    <i class="fas fa-thumbs-down mr-1.5"></i> Rechazar
                                </button>
                            </div>
                        </div>

                        {{-- Feedback de procesamiento síncrono/carga --}}
                        <div wire:loading wire:target="verificar,aprobar,rechazar" class="mt-2 text-center text-xs text-blue-600 dark:text-blue-400 animate-pulse font-medium">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Procesando cambios en el servidor...
                        </div>
                        @else
                        <div class="mt-4 p-3 bg-gray-100 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-700/60 rounded-xl text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-lock mr-1"></i> Este contrato ya ha concluido su flujo de resoluciones y auditorías.
                            </p>
                        </div>
                        @endif

                        {{-- Panel de Evidencia (Documento Oficial Adjunto PDF) --}}
                        @if($contrato->contrato_path)
                        <div class="p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">
                                <i class="fas fa-file-pdf mr-1 text-red-500"></i> Documento Oficial Adjunto
                            </p>
                            <a href="{{ Storage::url($contrato->contrato_path) }}" target="_blank" class="block group relative rounded-lg overflow-hidden border dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                                <div class="w-full h-24 flex flex-col items-center justify-center text-xs text-blue-600 dark:text-blue-400 font-medium group-hover:bg-gray-200/50 dark:group-hover:bg-gray-800/50 transition p-2">
                                    <span class="truncate max-w-[240px] text-gray-700 dark:text-gray-300 mb-1 font-mono text-[11px]">{{ basename($contrato->contrato_path) }}</span>
                                    <span><i class="fas fa-external-link-alt mr-1 text-[10px]"></i> Abrir y examinar PDF</span>
                                </div>
                            </a>
                        </div>
                        @endif

                        {{-- Información de Integridad Criptográfica --}}
                        @if($contrato->document_hash)
                        <div class="flex items-start p-3 bg-teal-50 dark:bg-teal-900/20 rounded-xl border border-teal-100 dark:border-teal-900/30">
                            <div class="mt-0.5 text-teal-600 dark:text-teal-400"><i class="fas fa-shield-check text-base"></i></div>
                            <div class="ml-3 min-w-0">
                                <p class="text-xs font-bold text-teal-900 dark:text-teal-300 uppercase">Firma Digital (Criptografía)</p>
                                <p class="text-[10px] text-teal-700 dark:text-teal-400 mt-0.5 font-mono break-all leading-tight">
                                    SHA-256: {{ $contrato->document_hash }}
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
                            <i class="fas fa-database mr-1"></i> Marcos contractuales totales
                        </span>
                        <span class="px-2.5 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full font-bold font-mono">
                            {{ App\Models\Contrato::count() }}
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- =========================================================================
         MODALES DE ACCIÓN (Estructura fluida idéntica a Denuncias)
         ========================================================================= --}}

    {{-- MODAL 1: CONFIRMAR VERIFICACIÓN --}}
    @if($mostrarModalVerificar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('mostrarModalVerificar', false)"></div>
        <div class="relative w-full max-w-md mx-auto z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mb-4">
                        <i class="fas fa-clipboard-check text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Verificar Contrato?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Confirmas que has revisado los términos jurídicos y el PDF adjunto de este convenio. Tu ID de usuario quedará registrado como el verificador oficial.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('mostrarModalVerificar', false)" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="verificar" class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm transition shadow-sm" wire:click="$set('mostrarModalVerificar', false)">
                        Sí, Verificar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 2: APROBAR CONTRATO (FIRMA Y RATIFICACIÓN) --}}
    @if($mostrarModalAprobar)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('mostrarModalAprobar', false)"></div>
        <div class="relative w-full max-w-md mx-auto z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 mb-4">
                        <i class="fas fa-stamp text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">¿Firmar y Aprobar Contrato?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Al ratificar la aprobación, el contrato pasará a estado activo y se aplicarán los parámetros económicos indicados en el panel izquierdo de forma inmediata.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button" wire:click="$set('mostrarModalAprobar', false)" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                        Abortar
                    </button>
                    <button type="button" wire:click="aprobar" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition shadow-md font-semibold" wire:click="$set('mostrarModalAprobar', false)">
                        Emitir y Firmar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL 3: RECHAZAR CONTRATO (REQUIERE MOTIVO) --}}
    @if($modalRechazo)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="$set('modalRechazo', false)"></div>
        <div class="relative w-full max-w-md mx-auto z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center justify-center h-9 w-9 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 mr-2">
                            <i class="fas fa-times-circle text-base"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Rechazar Contrato</h3>
                    </div>
                    <button wire:click="$set('modalRechazo', false)" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">
                            Motivo del rechazo <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="motivo_rechazo" rows="4" placeholder="Describe el motivo detallado por el cual se deniega el contrato..."
                            class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-red-500 focus:border-red-500 transition resize-none @error('motivo_rechazo') border-red-500 @enderror"></textarea>
                        @error('motivo_rechazo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="button" wire:click="$set('modalRechazo', false)" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm transition">
                            Cancelar
                        </button>
                        <button type="button" wire:click="rechazar" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-sm transition shadow-sm font-semibold">
                            Confirmar Rechazo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>