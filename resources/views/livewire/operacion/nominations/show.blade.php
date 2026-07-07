@section('page-title', 'Ver Nominación')
@section('page-description', 'Información generada de una nominación')

<div>
    {{-- Mensaje de éxito --}}
    @if(session()->has('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-800 font-medium">
                {{ session('success') }}
            </p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">

            {{-- FORMULARIO --}}
            <div class="p-6 md:p-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-signature mr-2 text-blue-500"></i>
                    Información de la Nominación
                </h2>

                <div class="space-y-6">

                    {{-- Número de Trámite --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Número de Trámite</label>
                        <input type="text" value="{{ $nomination->numero_tramite }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                    </div>

                    {{-- Nominador --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Nominador</label>
                        <input type="text"
                            value="{{ $nomination->nominator?->first_name }} {{ $nomination->nominator?->last_name }}"
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                    </div>

                    {{-- Candidato --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Candidato</label>
                        <input type="text"
                            value="{{ $nomination->candidate?->first_name }} {{ $nomination->candidate?->last_name }}"
                            readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                    </div>

                    {{-- Rol --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Rol</label>
                        <input type="text" value="{{ $nomination->role_name }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                    </div>

                    {{-- Unidad que emite --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Unidad que Emite</label>
                        <input type="text" value="{{ $nomination->issuer_type }}" readonly
                            class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                    </div>

                    {{-- Institución --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Institución</label>
                        <input type="text" value="{{ $nomination->released_by }}" readonly
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

                            @if($nomination->document_path)
                            <a href="{{ route('ver.documento', ['path' => base64_encode($nomination->document_path)]) }}"
                                target="_blank"
                                class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                ABRIR DOCUMENTO
                            </a>
                            @else
                            <span class="text-gray-400 text-xs">No hay documento adjunto</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Fecha de Emisión --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">Fecha de Emisión</label>
                            <input type="text"
                                value="{{ $nomination->fecha_emision?->format('d/m/Y') }}"
                                readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                        </div>

                        {{-- Inicio de Vigencia --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">Inicio de Vigencia</label>
                            <input type="text"
                                value="{{ $nomination->fecha_inicio_vigencia?->format('d/m/Y') }}"
                                readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                        </div>

                        {{-- Fin de Vigencia --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">Fin de Vigencia</label>
                            <input type="text"
                                value="{{ $nomination->fecha_fin_vigencia?->format('d/m/Y') }}"
                                readonly
                                class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-300">
                        </div>

                    </div>


                    {{-- Observaciones --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Observaciones</label>
                        <textarea readonly rows="3"
                            class="w-full px-4 py-3 bg-white border rounded-lg">{{ $nomination->observaciones }}</textarea>
                    </div>
                    {{-- ESTADO GENERAL --}}
                    <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Estado del Trámite
                        </h3>

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white {{ $nomination->estadoColor() }}">
                            {{ $nomination->estadoLabel() }}
                        </span>
                    </div>

                    {{-- INFORMACIÓN DE VERIFICACIÓN --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Verificación
                        </h3>

                        @if($nomination->verified_by)
                        <p class="text-sm text-gray-700">
                            <strong>Verificado por:</strong>
                            {{ $nomination->verifier?->first_name }} {{ $nomination->verifier?->last_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <strong>Fecha:</strong>
                            {{ $nomination->verified_at?->format('d/m/Y H:i') }}
                        </p>
                        @else
                        <p class="text-xs text-gray-400">Aún no verificado</p>
                        @endif
                    </div>

                    {{-- INFORMACIÓN DE APROBACIÓN --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Aprobación
                        </h3>

                        @if($nomination->approved_by)
                        <p class="text-sm text-gray-700">
                            <strong>Aprobado por:</strong>
                            {{ $nomination->approver?->first_name }} {{ $nomination->approver?->last_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <strong>Fecha:</strong>
                            {{ $nomination->approved_at?->format('d/m/Y H:i') }}
                        </p>
                        @else
                        <p class="text-xs text-gray-400">Aún no aprobado</p>
                        @endif
                    </div>

                    {{-- INFORMACIÓN DE RECHAZO --}}
                    <div class="mt-6 p-4 bg-red-50 rounded-xl border border-red-200">
                        <h3 class="text-sm font-semibold text-red-600 uppercase tracking-wider mb-3">
                            Rechazo
                        </h3>

                        @if($nomination->rejected_by)
                        <p class="text-sm text-gray-700">
                            <strong>Rechazado por:</strong>
                            {{ $nomination->rejectedBy?->first_name }} {{ $nomination->rejectedBy?->last_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            <strong>Fecha:</strong>
                            {{ $nomination->rejected_at?->format('d/m/Y H:i') }}
                        </p>
                        @else
                        <p class="text-xs text-gray-400">No existe rechazo registrado</p>
                        @endif
                    </div>

                    {{-- BOTÓN --}}
                    <div class="pt-6 border-t">
                        <a href="{{ route('nominations.index') }}"
                            class="w-full inline-flex justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Regresar a la lista de nominaciones
                        </a>
                    </div>

                </div>
            </div>

            {{-- PANEL INFORMATIVO --}}
            <div class="p-6 md:p-8 bg-gray-50">
                <div class="text-center">
                    <i class="fas fa-shield-alt text-4xl text-blue-500 mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Proceso Formal</h3>
                    <p class="text-gray-600">
                        La nominación está registrada con auditoría, hash de integridad y trazabilidad completa.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>