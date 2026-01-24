@section('page-title', 'Buscar Nominación')
@section('page-description', 'Iniciar proceso de nominaciones')

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
                        <select wire:model="candidate_user_id"
                            class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-700 border">
                            <option value="">Seleccione un usuario</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->last_name }} {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('candidate_user_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Rol --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Rol *</label>
                            <select wire:model="role_name" wire:change="rolCambiado"
                                class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-700 border">
                                    <option value="">Seleccione un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                
                        @error('role_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Institucion que certifica el documento --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Institución</label>
                        <select wire:model="released_by" 
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg">
                            <option value="">Seleccione una institución</option>
                            @foreach($instituciones as $institucion)
                                <option value="{{ $institucion->nombre }}">{{ $institucion->nombre }}</option>
                            @endforeach
                        </select>
                        @error('released_by') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Origen --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Origen *</label>
                        <select wire:model="issuer_type"
                            class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-700 border">
                            <option value="DMQ">DMQ</option>
                            <option value="JUNTA_PARROQUIAL">Junta Parroquial</option>
                        </select>
                        @error('issuer_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- PDF --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Documento PDF *</label>
                        <input type="file" wire:model="pdf" accept="application/pdf"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg">
                        @error('pdf') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                        <div wire:loading wire:target="pdf" class="text-sm text-blue-500 mt-2">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Subiendo archivo...
                        </div>
                    </div>

                    {{-- Fecha de Emisión --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Fecha de Emisión *</label>
                        <input type="date" wire:model="fecha_emision"
                            class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-700 border">
                        @error('fecha_emision') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fecha de Inicio de Funciones --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Fecha de Inicio de Funciones *</label>
                        <input type="date" wire:model="fecha_inicio_vigencia"
                            class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-700 border">
                        @error('fecha_inicio_vigencia') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fecha de Terminación de Funciones --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Fecha de Terminación de Funciones *</label>
                        <input type="date" wire:model="fecha_fin_vigencia"
                            class="w-full px-4 py-3 rounded-lg bg-white dark:bg-gray-700 border">
                        @error('fecha_fin_vigencia') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Observaciones</label>
                        <textarea wire:model="observaciones" rows="3"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg"></textarea>
                        @error('observaciones') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- BOTÓN --}}
                    <div class="pt-6 border-t">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                            <span wire:loading.remove>
                                <i class="fas fa-save mr-2"></i> Registrar Nominación
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin mr-2"></i> Procesando...
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
                        La nominación queda registrada con número de trámite, auditoría y hash de integridad.
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
