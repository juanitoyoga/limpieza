<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }} — LimpiaTuRincón</title>

    {{-- CDN directo para asegurar disponibilidad independiente del bundle principal --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-50 min-h-screen flex flex-col justify-between">

    <!-- Encabezado superior -->
    <header class="bg-white border-b border-gray-200 py-3 px-4 sm:px-6 lg:px-8 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">

            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('general.info') }}" class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-500 to-green-700 rounded-lg shadow">
                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="LTR logo">
                            <rect width="24" height="24" fill="none" />
                            <text x="5" y="16" font-family="Inter, system-ui, -apple-system, Arial, sans-serif"
                                font-size="15" font-weight="700" text-anchor="middle" alignment-baseline="middle" fill="#FFFFFF">L</text>
                            <text x="12" y="16" font-family="Inter, system-ui, -apple-system, Arial, sans-serif"
                                font-size="15" font-weight="700" text-anchor="middle" alignment-baseline="middle" fill="#98FB98">T</text>
                            <text x="19" y="16" font-family="Inter, system-ui, -apple-system, Arial, sans-serif"
                                font-size="15" font-weight="700" text-anchor="middle" alignment-baseline="middle" fill="#87CEEB">R</text>
                        </svg>
                    </div>
                    <div class="hidden md:block">
                        <h1 class="text-xl font-bold text-gray-900 leading-tight">
                            <span class="text-green-700">Limpia</span><span class="text-green-500">Tu</span><span class="text-blue-600">Rincon</span>
                        </h1>
                        <p class="text-xs text-gray-500">Operaciones</p>
                    </div>
                </a>
            </div>

            <!-- Información central / Estado -->
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
                        <div class="text-xs text-gray-500" id="current-time">
                            {{ now()->format('h:i A') }}
                        </div>
                    </div>
                </div>

                <!-- Separador -->
                <div class="h-6 w-px bg-gray-300"></div>

                <!-- Estado del sistema -->
                <div class="flex items-center space-x-2">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </div>
                    <span class="text-sm font-medium text-gray-600">Sistema Operativo</span>
                </div>
            </div>

        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-grow flex items-center justify-center">
        <!-- <x-estado-bloqueado
            :titulo="$titulo"
            :mensaje="$mensaje"
            :detalles="$detalles"
            :ruta-regreso="$rutaRegreso"
            :texto-boton="$textoBoton"
            :cerrar-pestana="true" /> -->
        {{ $slot }}

    </main>

</body>

</html>