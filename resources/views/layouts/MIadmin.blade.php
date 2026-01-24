<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>

    <link rel="stylesheet" href="{{ asset('css/sky-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sky-forms-green.css') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex h-screen bg-gray-100">


        <!-- Contenedor para aside y contenido principal -->

            <!-- Aside -->
            <aside class="w-64 relative z-10 bg-gradient-to-b from-green-100 to-blue-100 p-4 shadow-md h-full">
                <h2 class="text-lg font-bold mb-4 text-blue-700">⚙️ Administración</h2>
                @livewire('admin.aside-menu')
            </aside>

    <!-- Contenedor principal -->
    <div class="flex flex-col flex-1">
        <!-- Header -->
        <header class="bg-gradient-to-r from-green-200 to-blue-200 shadow-md p-4 flex justify-between items-center">
            <div class="flex space-x-4">
                <a href="{{ route('dashboard.home') }}" class="flex items-center font-semibold text-blue-500 hover:text-blue-800 transition duration-200">
                    <x-heroicon-o-chart-bar class="w-5 h-5 mr-2"/>
                    Dashboard
                </a>

                <a href="{{ route('general.info') }}" 
                class="flex items-center gap-2 px-3 py-2 rounded transition hover:bg-blue-300">
                <x-heroicon-o-home class="w-5 h-5 mr-2"/>
                 <span style="color: #008000; font-weight: bold;">Limpia</span> 
                 <span style="color: #32CD32; font-weight: bold;">Tu</span> 
                 <span style="color: #4682B4; font-weight: bold;">Rincon</span>
             </a>                

            </div>
            <div class="flex items-center space-x-6">
                <span class="text-sm text-gray-700">{{ Auth::user()->first_name . ' ' . Auth::user()->last_name . ' 🛡️ ' }}</span>
                <span class="text-sm text-gray-500">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM, YYYY - H:mm') }}</span>
            </div>
            <div class="flex space-x-4">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center font-semibold text-red-500 hover:text-red-800 transition duration-200">
                        <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 mr-2"/>
                        Logout
                    </button>
                </form>
            </div>
        </header>
                    <!-- Contenido principal -->
                    <main class="flex-1 p-6 overflow-y-auto">
                        {{ $slot }}
                    </main>

    </div>

    @livewireScripts
</body>
</html>