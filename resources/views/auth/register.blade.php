<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-section {
            scroll-margin-top: 1rem;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-6">
    
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
<div class="mb-8 flex justify-between items-center">
    <!-- Bloque izquierdo -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Registro de Usuarios</h1>
        <p class="text-gray-600 mt-2">
            Complete la información para registrarse. 
            Los campos marcados con <span class="text-red-500">*</span> son obligatorios.
        </p>
    </div>

    <!-- Bloque derecho: logo -->
    <div>
        <h1 class="logo clearfix">
            <a href="#" title="¡Sé un héroe de la limpieza!">
                <span class="text-green-700 font-bold">Limpia</span> 
                <span class="text-green-500 font-bold">Tu</span> 
                <span class="text-blue-600 font-bold">Rincon</span>
            </a>
        </h1>
    </div>
</div>


        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Pestañas de secciones (opcional) -->
            <div class="border-b border-gray-200">
                <div class="flex overflow-x-auto">
                    <button class="tab-button px-6 py-4 font-medium text-blue-600 border-b-2 border-blue-600 whitespace-nowrap">
                        <i class="fas fa-user mr-2"></i>Información Personal
                    </button>
                    <button class="tab-button px-6 py-4 font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">
                        <i class="fas fa-phone mr-2"></i>Contacto
                    </button>

                </div>
            </div>

            <!-- Contenido del formulario -->
            <form class="p-6 md:p-8"  method="POST" action="{{ route('register') }}">
                @csrf
    
                <!-- SECCIÓN 1: Información Básica -->
                <div class="form-section mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full mr-3"></div>
                        <h2 class="text-xl font-bold text-gray-800">Información Básica</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        
                        <!-- Columna Izquierda -->
                        <div class="space-y-6">

                            <!-- Tipo Documento -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tipo ID
                                </label>
                                <select name="tipo_id"  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                    <option value="">Seleccionar Tipo ID</option>
                                    <option value="Cedula">Cedula</option>
                                    <option value="RUC">RUC</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                </select>
                                @error('tipo_id') <span class="text-red-500">{{ $message }}</span> @enderror

                            </div>

                            <!-- Nombres -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nombres <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="first_name" 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                           placeholder="Nombres ...">
                                           @error('first_name') <span class="text-red-500">{{ $message }}</span> @enderror

                                </div>
                            </div>


                            <!-- Correo Electrónico -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                           placeholder="ejemplo@correo.com">
                                           @error('email') <span class="text-red-500">{{ $message }}</span> @enderror

                                </div>
                            </div>

                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-6">

                            <!-- Identificación -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Cédula/RUC/Pasaporte <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-id-card text-gray-400"></i>
                                    </div>
                                    <input name="nro_id"   type="text" 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                           placeholder="Ej: 1234567890">
                                           @error('nro_id') <span class="text-red-500">{{ $message }}</span> @enderror

                                </div>
                            </div>


                            <!-- Apellidos -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Apellidos <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="last_name" 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                           placeholder="Apellidos ...">
                                           @error('last_name') <span class="text-red-500">{{ $message }}</span> @enderror

                                </div>
                            </div>                            


                            <!-- Teléfono -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Teléfono <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input name="phone"  type="tel" 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition"
                                           placeholder="Ej: 0991234567">
                                           @error('phone') <span class="text-red-500">{{ $message }}</span> @enderror

                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: Seguridad -->
                <div class="form-section mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-3"></div>
                        <h2 class="text-xl font-bold text-gray-800">Contraseña  </h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Columna Izquierda -->
                        <div class="space-y-6">
                            <x-label for="password" value="{{ __('Password') }}" />
                            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />

                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-6">
                            <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                            <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                <!-- SECCION 3 Adicionales -->
                <div class="form-section mb-10">
                    <div class="flex items-center mb-6">
                        <div class="w-2 h-8 bg-green-500 rounded-full mr-3"></div>
                        <h2 class="text-xl font-bold text-gray-800">Filiacion  </h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Columna Izquierda -->
                        <div class="space-y-6">                    
                            <!-- Género -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Género
                                </label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="flex items-center">
                                        <input type="radio" name="gender" value="M" class="mr-2 text-blue-600">
                                        <span>Masculino</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="gender" value="F" class="mr-2 text-blue-600">
                                        <span>Femenino</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="gender" value="Otro" class="mr-2 text-blue-600">
                                        <span>Otro</span>
                                    </label>
                                </div>
                            </div>  
                        </div>
                        <!-- Columna Derecha -->
                        <div class="space-y-6">
                            <!-- Fecha Nacimiento -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Fecha de Nacimiento
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <input name="birthdate" type="date" 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                </div>
                            </div>
                        </div>                                              
                </div>        
                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif
                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-gray-200">
                    <div class="mb-4 sm:mb-0">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="text-sm">Todos los derechos reservados © 2024</span>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('login') }}" 
                                class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Ya se registro?
                        </a>
                        
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Registrarse
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Contador de campos -->
        <div class="mt-6 text-center">
            <div class="inline-flex items-center bg-white px-4 py-2 rounded-full shadow">
                <span class="text-gray-600 mr-2">Campos completados:</span>
                <span class="font-bold text-blue-600">0/16</span>
            </div>
        </div>
    </div>

    <script>
        // Interactividad simple para las pestañas
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Remover clase activa de todas las pestañas
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('text-blue-600', 'border-blue-600');
                    btn.classList.add('text-gray-500');
                });
                
                // Agregar clase activa a la pestaña clickeada
                this.classList.remove('text-gray-500');
                this.classList.add('text-blue-600', 'border-blue-600');
                
                // Aquí iría la lógica para mostrar/ocultar secciones
                const sectionName = this.textContent.trim();
                console.log(`Mostrar sección: ${sectionName}`);
            });
        });

        // Contador de campos llenados
        const inputs = document.querySelectorAll('input, select, textarea');
        const counter = document.querySelector('.font-bold.text-blue-600');
        
        function updateCounter() {
            let filled = 0;
            inputs.forEach(input => {
                if (input.value.trim() !== '') {
                    filled++;
                }
            });
            counter.textContent = `${filled}/${inputs.length}`;
        }
        
        inputs.forEach(input => {
            input.addEventListener('input', updateCounter);
            input.addEventListener('change', updateCounter);
        });
        
        // Inicializar contador
        updateCounter();
    </script>

</body>
</html>
