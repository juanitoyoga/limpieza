@section('page-title', 'Nuevo Barrio')
@section('page-description', 'Complete el formulario para registrar un nuevo barrio en el sistema')

{{-- livewire/admin/barrios/create.blade.php --}}

<div>
    <!-- Encabezado de la página -->


    <!-- Mensajes de sesión -->
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

    <!-- Tarjeta principal -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">
            
            <!-- Columna del formulario -->
            <div class="p-6 md:p-8">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                        <i class="fas fa-edit mr-2 text-blue-500"></i>
                        Información del Barrio
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Todos los campos son obligatorios
                    </p>
                </div>

                <form wire:submit.prevent="store" class="space-y-6">
                    <!-- Campo: Identificación GeoPis -->
                    <div>
                        <label for="id_DMQ" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Identificación GeoPis
                        </label>
                        <input
                            id="id_DMQ"
                            wire:model="id_DMQ"
                            type="text"
                            placeholder="Ej: DMQ-001"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        @error('id_DMQ')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campo: Nombre del Barrio -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Nombre del Barrio
                        </label>
                        <input
                            id="nombre"
                            wire:model="nombre"
                            type="text"
                            placeholder="Ej: Centro Histórico"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        @error('nombre')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campo: Sector -->
                    <div>
                        <label for="sector" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Sector
                        </label>
                        <input
                            id="sector"
                            wire:model="sector"
                            type="text"
                            placeholder="Ej: Norte"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        @error('sector')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Campo: Parroquia -->
                    <div>
                        <label for="parroquia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <span class="text-red-500">*</span> Parroquia
                        </label>
                        <input
                            id="parroquia"
                            wire:model="parroquia"
                            type="text"
                            placeholder="Ej: San Sebastián"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 transition duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                        />
                        @error('parroquia')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Botón de envío -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="store"
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="store">
                                <i class="fas fa-save mr-2"></i>
                                Registrar Barrio
                            </span>
                            <span wire:loading wire:target="store">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Procesando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Columna informativa -->
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50">
                <div class="h-full flex flex-col justify-center">
                    <div class="mb-8 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                            <i class="fas fa-city text-3xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                            Sistema de Barrios
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Los barrios son la unidad básica de organización territorial en el sistema.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Información 1 -->
                        <div class="flex items-start p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-info-circle text-blue-500 dark:text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">Código único</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    La identificación GeoPis debe ser única para cada barrio registrado.
                                </p>
                            </div>
                        </div>

                        <!-- Información 2 -->
                        <div class="flex items-start p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-shield-alt text-green-500 dark:text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">Datos seguros</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Toda la información se almacena de forma segura en la base de datos.
                                </p>
                            </div>
                        </div>

                        <!-- Información 3 -->
                        <div class="flex items-start p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas fa-sync-alt text-purple-500 dark:text-purple-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">Actualización inmediata</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Los cambios se reflejan instantáneamente en el sistema.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contador o información adicional -->
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">
                                <i class="fas fa-database mr-2"></i>
                                Total de barrios
                            </span>
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full font-medium">
                                {{ App\Models\Barrio::count() }}
                            </span>
                        </div>
                    </div>
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

<!-- Script para mejoras de UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus automático en el primer campo
    const firstInput = document.querySelector('#id_DMQ');
    if (firstInput) {
        setTimeout(() => {
            firstInput.focus();
            firstInput.select();
        }, 300);
    }
    
    // Prevenir envío doble del formulario
    let formSubmitted = false;
    const form = document.querySelector('form[wire\\:submit\\.prevent]');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (formSubmitted) {
                e.preventDefault();
                return false;
            }
            formSubmitted = true;
            setTimeout(() => { formSubmitted = false; }, 3000);
        });
    }
});
</script>