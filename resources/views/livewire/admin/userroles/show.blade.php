@section('page-title', 'Roles de Usuario')
@section('page-description', 'Consulta del Rol Asignado al Usuario')

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


    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">

            {{-- Columna principal --}}
            <div class="p-6 md:p-8">

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                        <i class="fas fa-id-badge mr-2 text-blue-500"></i>
                        Información del Rol Asignado
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Registro oficial del rol del usuario en el sistema LimpiaTuRincón.
                    </p>
                </div>

                <div class="space-y-6">

                    {{-- ID --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            ID del Registro
                        </label>
                        <div class="mt-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{ $userrole->id }}
                        </div>
                    </div>

                    {{-- Usuario --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Usuario
                        </label>
                        <div class="mt-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{ $userrole->user->first_name . ' ' . $userrole->user->last_name  ?? 'Sin asignar' }}
                        </div>
                    </div>

                    {{-- Rol --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Rol
                        </label>
                        <div class="mt-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{ $userrole->role->name ?? 'No definido' }}
                        </div>
                    </div>

                    {{-- Documentos --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Documento de Nombramiento
                        </label>
                        <div class="mt-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{ $userrole->appointment_document ?? 'No registrado' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Documento de Cesación
                        </label>
                        <div class="mt-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{ $userrole->cessation_document ?? 'No aplica' }}
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Fecha de Inicio
                        </label>
                        <div class="mt-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{ $userrole->started_at?->format('Y-m-d') ?? '---' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Fecha de Finalización
                        </label>
                        <div class="mt-1 px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                            {{ $userrole->ended_at?->format('Y-m-d') ?? 'Actualidad' }}
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Estado Actual
                        </label>
                        <span class="mt-1 inline-block px-3 py-2 rounded-lg font-medium 
                            {{ $userrole->is_active ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                            {{ $userrole->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    {{-- Botón --}}
                    <div class="pt-6 border-t dark:border-gray-700">
                        <a href="{{ route('userroles.index') }}"
                           class="w-full block text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Regresar al listado
                        </a>
                    </div>

                </div>
            </div>

            {{-- Columna derecha informativa --}}
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50">

                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                        <i class="fas fa-users-cog text-3xl text-blue-600 dark:text-blue-400"></i>
                    </div>

                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                        Información del Rol
                    </h3>

                    <p class="text-gray-600 dark:text-gray-400">
                        Los roles determinan el conjunto de permisos y responsabilidades dentro del sistema.
                    </p>
                </div>

            {{-- Columna informativa --}}
            <div class="p-6 md:p-8 bg-gray-50 dark:bg-gray-900/50">

                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-4">
                        <i class="fas fa-gavel text-3xl text-blue-600 dark:text-blue-400"></i>
                    </div>

                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                        Sistema de Contravenciones
                    </h3>

                    <p class="text-gray-600 dark:text-gray-400">
                        Control interno de infracciones según la Ordenanza Municipal 332.
                    </p>
                </div>

                <div class="space-y-4">

                    {{-- Info 1 --}}
                    <div class="flex items-start p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="mt-1">
                            <i class="fas fa-gavel text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Responsabilidad Legal</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Los roles activos generan responsabilidades directas sobre procesos administrativos y comunitarios.
                            </p>
                        </div>
                    </div>

                    {{-- Info 2 --}}
                    <div class="flex items-start p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="mt-1">
                            <i class="fas fa-user-shield text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Roles Especiales</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Un usuario puede tener múltiples roles como Auditor, Dirigente o Funcionario.
                            </p>
                        </div>
                    </div>

                    {{-- Info 3 --}}
                    <div class="flex items-start p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="mt-1">
                            <i class="fas fa-sync-alt text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Periodo de Asignación</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Los roles pueden ser activados y desactivados según documentos oficiales de designación.
                        </div>
                    </div>

                </div>                


                {{-- Contador --}}
                <div class="mt-8 pt-6 border-t dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">
                            <i class="fas fa-database mr-2"></i>
                            Total roles asignados
                        </span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">
                            {{ App\Models\Userrole::count() }}
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
