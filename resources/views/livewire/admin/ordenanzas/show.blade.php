@section('page-title', 'Contravenciones')
@section('page-description', 'Consulta de los datos de la Contravención')

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

            {{-- Columna del formulario --}}
            <div class="p-6 md:p-8">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                        <i class="fas fa-gavel mr-2 text-blue-500"></i>
                        Información de la Contravención
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Registro perteneciente al sistema LimpiaTuRincón.
                    </p>
                </div>

                <form action="{{ route('ordenanzas.index') }}" method="get" class="space-y-6">

                    {{-- ID --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nro. ID
                        </label>
                        <strong class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg">
                            {{ $ordenanza->id }}
                        </strong>
                    </div>

                    {{-- Código --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Código
                        </label>
                        <strong class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg">
                            {{ $ordenanza->codigo }}
                        </strong>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Descripción
                        </label>
                        <textarea
                            rows="3"
                            readonly
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg text-gray-900 dark:text-white"
                        >{{ $ordenanza->descripcion }}</textarea>
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo
                        </label>
                        <strong class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg">
                            {{ $ordenanza->tipo }}
                        </strong>
                    </div>

                    {{-- Nivel de Gravedad --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nivel de Gravedad
                        </label>
                        <strong class="w-full px-4 py-3 bg-white dark:bg-gray-700 border rounded-lg">
                            {{ $ordenanza->nivel_gravedad }}
                        </strong>
                    </div>

                    {{-- Botón --}}
                    <div class="pt-6 border-t dark:border-gray-700">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>
                            Regresar al listado
                        </button>
                    </div>

                </form>
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
                            <i class="fas fa-hashtag text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Clasificación</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Cada contravención posee un código único y una categoría (Primera, Segunda o Tercera Clase).
                            </p>
                        </div>
                    </div>

                    {{-- Info 2 --}}
                    <div class="flex items-start p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="mt-1">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Nivel de gravedad</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                El sistema clasifica las infracciones en: Leve, Media o Grave.
                            </p>
                        </div>
                    </div>

                    {{-- Info 3 --}}
                    <div class="flex items-start p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="mt-1">
                            <i class="fas fa-shield-alt text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-white">Información segura</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Los registros están protegidos y vinculados a la ordenanza vigente.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Contador --}}
                <div class="mt-8 pt-6 border-t dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">
                            <i class="fas fa-database mr-2"></i>
                            Total de contravenciones
                        </span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full font-medium">
                            {{ App\Models\Ordenanza332::count() }}
                        </span>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
