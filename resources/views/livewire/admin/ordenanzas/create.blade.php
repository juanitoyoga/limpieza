
@section('page-title', 'Nueva Contravencion')
@section('page-description', 'Complete el formulario para registrar una nueva contravencion en el sistema')

{{-- livewire/admin/barrios/create.blade.php --}}

<div>
    <!-- Mensaje de sesión -->
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

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">
            
            <!-- Columna formulario -->
            <div class="p-6 md:p-8">
                
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-edit mr-2 text-blue-500"></i>
                    Información de la Contravención
                </h2>

                <form wire:submit.prevent="store" class="space-y-6">

                    <!-- CÓDIGO -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Código
                        </label>

                        <input
                            wire:model.live="codigo"
                            type="text"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 
                                   rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >

                        @error('codigo')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Descripción
                        </label>

                        <input
                            wire:model.live="descripcion"
                            type="text"
                            placeholder="Ej: Botar basura en el espacio público"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 
                                   rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >

                        @error('descripcion')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- TIPO -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Tipo
                        </label>

                        <select
                            wire:model.live="tipo"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 
                                   rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Seleccione...</option>
                            <option value="Primera Clase">Primera Clase</option>
                            <option value="Segunda Clase">Segunda Clase</option>
                            <option value="Tercera Clase">Tercera Clase</option>
                        </select>

                        @error('tipo')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- NIVEL GRAVEDAD -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Nivel de Gravedad
                        </label>

                        <select
                            wire:model.live="nivel_gravedad"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 
                                   rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Seleccione...</option>
                            <option value="Leve">Leve</option>
                            <option value="Medio">Medio</option>
                            <option value="Grave">Grave</option>
                        </select>

                        @error('nivel_gravedad')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- BOTÓN -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove>Registrar Contravención</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Columna derecha (informativa) -->
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    Información Importante
                </h3>

                <p class="text-gray-700 dark:text-gray-300">
                    Complete todos los campos obligatorios para registrar la contravención en el sistema.
                </p>
            </div>
        </div>
    </div>
</div>


    <!-- Errores globales -->
    @if($errors->any())
        <div class="mt-6">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">
                            Por favor, corrija los siguientes errores:
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

