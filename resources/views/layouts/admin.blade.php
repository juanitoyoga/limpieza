<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'LimpiaTuRincon'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/icons/icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AlpineJS para interacciones -->
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    @livewireStyles()
    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false, userMenuOpen: false, notificationsOpen: false }">
    <!-- Barra superior fija -->
    <nav class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo y nombre de la app - Izquierda -->
                <div class="flex items-center">
                    <!-- Botón menú móvil -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <!-- Logo -->
                    <div class="flex items-center ml-2 lg:ml-0">
                        <a href="{{ route('general.info') }}" class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg shadow">
                                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg shadow">
                                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="LTR logo">
                                        <!-- Fondo transparente para mantener el gradiente del div -->
                                        <rect width="24" height="24" fill="none" />
                                        <!-- Letras: L T R -->
                                        <text x="5" y="16" font-family="Inter, system-ui, -apple-system, Arial, sans-serif"
                                            font-size="15" font-weight="700" text-anchor="middle" alignment-baseline="middle" fill="#008000">L</text>
                                        <text x="12" y="16" font-family="Inter, system-ui, -apple-system, Arial, sans-serif"
                                            font-size="15" font-weight="700" text-anchor="middle" alignment-baseline="middle" fill="#32CD32">T</text>
                                        <text x="19" y="16" font-family="Inter, system-ui, -apple-system, Arial, sans-serif"
                                            font-size="15" font-weight="700" text-anchor="middle" alignment-baseline="middle" fill="#4682B4">R</text>
                                    </svg>
                                </div>

                            </div>
                            <div class="hidden md:block">
                                <h1 class="text-xl font-bold text-gray-900">
                                    <span style="color: #008000; font-weight: bold;">Limpia</span>
                                    <span style="color: #32CD32; font-weight: bold;">Tu</span>
                                    <span style="color: #4682B4; font-weight: bold;">Rincon</span>
                                </h1>
                                <p class="text-xs text-gray-500">Administracion</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Información central - Solo desktop -->
                <div class="hidden lg:flex items-center space-x-6">
                    <!-- Fecha y hora actual -->
                    <div class="flex items-center space-x-2 text-sm">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900" id="current-date">
                                {{ now()->translatedFormat('l, d \de F \de Y') }}
                            </div>
                            <div class="text-gray-500" id="current-time">
                                {{ now()->format('h:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Separador -->
                    <div class="h-6 w-px bg-gray-300"></div>

                    <!-- Estado del sistema -->
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <div class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-75"></div>
                        </div>
                        <span class="text-sm text-gray-600">Sistema Operativo</span>
                    </div>
                </div>

                <!-- Información de usuario y acciones - Derecha -->
                <div class="flex items-center space-x-3">
                    <!-- Botón Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="hidden md:flex items-center space-x-2 px-4 py-2 bg-primary-50 text-primary-700 hover:bg-primary-100 rounded-lg transition-colors">
                        <i class="fas fa-chart-line"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <!-- Botón Información -->
                    <a href="#"
                        class="hidden md:flex items-center space-x-2 px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-info-circle"></i>
                        <span class="font-medium">Información</span>
                    </a>


                    <!-- Menú de usuario -->
                    <div class="relative">
                        <button @click="userMenuOpen = !userMenuOpen; notificationsOpen = false"
                            class="flex items-center space-x-3 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3">
                                <!-- Avatar -->
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-semibold shadow">
                                    @if(auth()->check())
                                    {{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->last_name ?? '', 0, 1) }}
                                    @else
                                    <i class="fas fa-user"></i>
                                    @endif
                                </div>

                                <!-- Información del usuario (solo desktop) -->
                                <div class="hidden md:block text-left">
                                    <div class="font-semibold text-gray-900">
                                        {{ auth()->check() ? auth()->user()->name : 'Invitado' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ auth()->check() ? auth()->user()->role->name ?? 'Usuario' : 'No autenticado' }}
                                    </div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down text-gray-500 text-sm" :class="{ 'rotate-180': userMenuOpen }"></i>
                        </button>

                        <!-- Dropdown del usuario -->
                        <div x-show="userMenuOpen"
                            @click.away="userMenuOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50 hidden"
                            :class="{ 'hidden': !userMenuOpen }"
                            style="display: none;">
                            <!-- Información del usuario -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="font-semibold text-gray-900">{{ auth()->check() ? auth()->user()->name : 'Invitado' }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->check() ? auth()->user()->email : 'guest@example.com' }}</p>
                                <div class="flex items-center mt-2">
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    <span class="text-xs text-gray-500">En línea</span>
                                </div>
                            </div>

                            <!-- Enlaces del usuario -->
                            <div class="py-2">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-circle mr-3 text-gray-500"></i>
                                    Mi perfil
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-3 text-gray-500"></i>
                                    Configuración
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-question-circle mr-3 text-gray-500"></i>
                                    Ayuda
                                </a>
                            </div>

                            <!-- Separador -->
                            <div class="border-t border-gray-100"></div>

                            <!-- Cerrar sesión -->
                            @if(auth()->check())
                            <form method="POST" action="{{ route('logout') }}" class="py-2">
                                @csrf
                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Cerrar sesión
                                </button>
                            </form>
                            @else
                            <div class="py-2">
                                <a href="{{ route('login') }}" class="flex items-center px-4 py-2 text-sm text-primary-600 hover:bg-primary-50">
                                    <i class="fas fa-sign-in-alt mr-3"></i>
                                    Iniciar sesión
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar para móvil -->
    <div x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 lg:hidden"
        :class="{ 'hidden': !sidebarOpen }"
        style="display: none;">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
        <div class="fixed inset-y-0 left-0 flex flex-col w-64 bg-white shadow-xl">
            <!-- Header del sidebar móvil -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-database text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900">{{ config('app.name', 'Sistema CRUD') }}</h2>
                        <p class="text-xs text-gray-500">Versión {{ config('app.version', '1.0.0') }}</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Navegación móvil -->
            <div class="flex-1 overflow-y-auto py-4">
                <nav class="space-y-1 px-3">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-900 bg-gray-100">
                        <i class="fas fa-chart-line mr-3 text-gray-500"></i>
                        Dashboard
                    </a>

                    <!-- Información -->
                    <a href="#"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        <i class="fas fa-info-circle mr-3 text-gray-500"></i>
                        Información
                    </a>

                    <!-- CRUDs Disponibles -->
                    <div class="mt-6">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Módulos CRUD
                        </h3>


                        <!-- Sidebar dinámico -->

                        @livewire('admin.aside-menu')



                    </div>

                    <!-- Enlaces de soporte -->
                    <div class="mt-6">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Soporte
                        </h3>
                        <div class="mt-2 space-y-1">
                            <a href="#"
                                class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                <i class="fas fa-question-circle mr-3 text-gray-400"></i>
                                Ayuda
                            </a>
                            <a href="#"
                                class="flex items-center px-3 py-2 text-sm rounded-lg text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                <i class="fas fa-book mr-3 text-gray-400"></i>
                                Documentación
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Pie del sidebar móvil -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            {{ auth()->check() ? auth()->user()->name : 'Invitado' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ auth()->check() ? auth()->user()->email : 'guest@example.com' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra lateral para desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:flex lg:w-64 lg:flex-col">
        <div class="flex flex-col flex-1 min-h-0 bg-white border-r border-gray-200">
            <!-- Logo y nombre -->
            <div class="flex items-center h-16 px-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-database text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900">{{ config('app.name', 'Sistema CRUD') }}</h2>
                        <p class="text-xs text-gray-500">Versión {{ config('app.version', '1.0.0') }}</p>
                    </div>
                </div>
            </div>

            <!-- Navegación principal -->
            <div class="flex-1 flex flex-col pt-5 pb-4">


                <!-- Sidebar dinámico -->

                @livewire('admin.aside-menu')


                <!-- Información del sistema en sidebar -->
                <div class="mt-auto px-4 py-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Estado del Sistema
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Base de datos</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Activa
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Cache</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Optimizado
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Sesiones</span>
                                <span class="text-xs font-medium text-gray-700">
                                    {{ \Illuminate\Support\Facades\Cache::get('online_users', 1) }} activas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="{{ auth()->check() ? 'lg:pl-64' : '' }} pt-16">
        <!-- Área de contenido -->
        <main class="py-6">
            <div class="px-4 sm:px-6 lg:px-8">
                <!-- Encabezado de página -->
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                @yield('page-title', 'Administracion')
                            </h1>
                            <p class="mt-2 text-sm text-gray-700">
                                @yield('page-description', 'Bienvenido al sistema de gestión')
                            </p>
                        </div>

                    </div>
                </div>

                <!-- Mensajes de sesión -->
                @if(session()->has('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">¡Éxito!</h3>
                            <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(session()->has('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(session()->has('warning'))
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">Advertencia</h3>
                            <p class="text-sm text-yellow-700 mt-1">{{ session('warning') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(session()->has('info'))
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-medium text-blue-800">Información</h3>
                            <p class="text-sm text-blue-700 mt-1">{{ session('info') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Para vistas Blade clásicas --}}
                @yield('content')

                {{-- Para componentes Livewire --}}
                {{ $slot ?? '' }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-sm text-gray-500 mb-4 md:mb-0">
                        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Sistema CRUD') }}. Todos los derechos reservados.</p>
                        <p class="mt-1">Desarrollado con Laravel, Tailwind CSS y Livewire.</p>
                    </div>

                    <div class="flex items-center space-x-6">
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">
                            Privacidad
                        </a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">
                            Términos
                        </a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">
                            Contacto
                        </a>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-heart text-red-500"></i>
                            <span class="text-sm text-gray-500">Hecho con pasión</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    @livewireScripts()

    @yield('scripts')
    <!-- Script para reloj en tiempo real -->
    <script>
        // Actualizar hora en tiempo real
        function updateDateTime() {
            const now = new Date();

            // Formatear fecha
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('es-ES', options);
            }

            // Formatear hora
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const formattedHours = hours % 12 || 12;
                timeElement.textContent = `${formattedHours}:${minutes} ${ampm}`;
            }
        }

        // Actualizar cada minuto
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('[x-show="userMenuOpen"]');
            const notificationsMenu = document.querySelector('[x-show="notificationsOpen"]');

            if (userMenu && !userMenu.contains(event.target) && !event.target.closest('[x-show="userMenuOpen"]')) {
                Alpine.store('userMenuOpen', false);
            }

            if (notificationsMenu && !notificationsMenu.contains(event.target) && !event.target.closest('[x-show="notificationsOpen"]')) {
                Alpine.store('notificationsOpen', false);
            }
        });
    </script>
    @stack('scripts')
</body>

</html>