<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CRUD - Laravel + Tailwind</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <!-- Información a la izquierda -->
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <div class="bg-primary-500 text-white p-3 rounded-lg">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Sistema CRUD</h1>
                        <p class="text-gray-600">Laravel + Tailwind CSS</p>
                        <div class="flex items-center mt-1">
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                            <span class="text-sm text-gray-500">Conectado a la base de datos</span>
                        </div>
                    </div>
                </div>

                <!-- Información a la derecha -->
                <div class="flex flex-col md:items-end">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="bg-primary-100 text-primary-700 p-2 rounded-full">
                                <i class="fas fa-user text-lg"></i>
                            </div>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Administrador</p>
                            <p class="text-sm text-gray-600">Último acceso: Hoy 10:30 AM</p>
                        </div>
                    </div>
                    <div class="mt-2 flex space-x-2">
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">Operaciones: 24</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">Registros: 156</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Formulario CRUD -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-edit mr-2 text-primary-500"></i>
                            Formulario de Productos
                        </h2>
                        <div class="flex space-x-2">
                            <button id="btn-create" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-plus mr-1"></i> Nuevo
                            </button>
                            <button id="btn-list" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
                                <i class="fas fa-list mr-1"></i> Ver Todos
                            </button>
                        </div>
                    </div>

                    <!-- Tabs para operaciones CRUD -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="flex space-x-1">
                            <button data-tab="create" class="tab-btn py-3 px-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-plus mr-2"></i> Crear
                            </button>
                            <button data-tab="read" class="tab-btn py-3 px-4 font-medium text-sm border-b-2 border-primary-500 text-primary-600">
                                <i class="fas fa-eye mr-2"></i> Consultar
                            </button>
                            <button data-tab="update" class="tab-btn py-3 px-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-edit mr-2"></i> Actualizar
                            </button>
                            <button data-tab="delete" class="tab-btn py-3 px-4 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <i class="fas fa-trash mr-2"></i> Eliminar
                            </button>
                        </nav>
                    </div>

                    <!-- Formulario CRUD -->
                    <form id="crud-form" class="space-y-6">
                        <!-- Campo ID (solo para Read, Update, Delete) -->
                        <div id="id-field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-key mr-2"></i> ID del Producto
                            </label>
                            <div class="flex">
                                <input type="text" name="product_id" class="flex-grow px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Ingrese el ID del producto">
                                <button type="button" class="bg-primary-500 text-white px-4 py-2 rounded-r-lg hover:bg-primary-600 transition">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>

                        <!-- Campos del formulario -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2"></i> Nombre del Producto
                                </label>
                                <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Ej: Laptop Pro">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-boxes mr-2"></i> Categoría
                                </label>
                                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">Seleccione una categoría</option>
                                    <option value="electronics">Electrónicos</option>
                                    <option value="clothing">Ropa</option>
                                    <option value="books">Libros</option>
                                    <option value="home">Hogar</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-dollar-sign mr-2"></i> Precio
                                </label>
                                <input type="number" step="0.01" name="price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-cubes mr-2"></i> Cantidad en Stock
                                </label>
                                <input type="number" name="stock" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2"></i> Descripción
                            </label>
                            <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Descripción detallada del producto..."></textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="available" id="available" class="h-4 w-4 text-primary-500 focus:ring-primary-400 border-gray-300 rounded">
                            <label for="available" class="ml-2 block text-sm text-gray-700">Disponible para venta</label>
                        </div>

                        <!-- Botones de acción -->
                        <div class="pt-4 border-t border-gray-200 flex justify-end space-x-4">
                            <button type="reset" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-redo mr-2"></i> Limpiar
                            </button>
                            <button type="submit" id="submit-btn" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition font-medium">
                                <i class="fas fa-save mr-2"></i> Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel lateral para instrucciones/estado -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                        Información de Operación
                    </h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-medium text-blue-800 mb-2">Operación Actual: <span id="current-operation">Consultar (Read)</span></h4>
                            <p class="text-sm text-blue-700" id="operation-description">
                                En esta operación puedes buscar un producto por su ID para visualizar toda su información. Completa el campo ID y haz clic en "Buscar".
                            </p>
                        </div>
                        
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <h4 class="font-medium text-yellow-800 mb-2">Instrucciones:</h4>
                            <ul class="text-sm text-yellow-700 space-y-2" id="operation-instructions">
                                <li><i class="fas fa-check-circle mr-2"></i> Introduce el ID del producto que deseas consultar</li>
                                <li><i class="fas fa-check-circle mr-2"></i> Haz clic en el botón "Buscar" para cargar la información</li>
                                <li><i class="fas fa-check-circle mr-2"></i> Los datos del producto aparecerán en los campos del formulario</li>
                                <li><i class="fas fa-exclamation-circle mr-2"></i> Los campos estarán en modo solo lectura para esta operación</li>
                            </ul>
                        </div>
                        
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h4 class="font-medium text-green-800 mb-2">Estado del Formulario:</h4>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-green-700" id="form-status">Listo para realizar consultas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer con diagramas e instrucciones -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Diagrama de flujo CRUD -->
                <div class="lg:col-span-2">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-project-diagram mr-3 text-primary-400"></i>
                        Diagrama de Flujo CRUD
                    </h3>
                    <div class="bg-gray-900 rounded-xl p-6">
                        <div class="flex flex-col md:flex-row items-center justify-between mb-8">
                            <!-- Diagrama visual simplificado -->
                            <div class="flex flex-col items-center mb-6 md:mb-0">
                                <div class="w-16 h-16 bg-primary-500 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-plus text-white text-2xl"></i>
                                </div>
                                <span class="text-sm font-medium">CREATE</span>
                                <div class="h-8 w-1 bg-primary-500 mt-2"></div>
                            </div>
                            
                            <div class="text-primary-400 text-2xl mx-4 hidden md:block">→</div>
                            
                            <div class="flex flex-col items-center mb-6 md:mb-0">
                                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-eye text-white text-2xl"></i>
                                </div>
                                <span class="text-sm font-medium">READ</span>
                                <div class="h-8 w-1 bg-green-500 mt-2"></div>
                            </div>
                            
                            <div class="text-primary-400 text-2xl mx-4 hidden md:block">→</div>
                            
                            <div class="flex flex-col items-center mb-6 md:mb-0">
                                <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-edit text-white text-2xl"></i>
                                </div>
                                <span class="text-sm font-medium">UPDATE</span>
                                <div class="h-8 w-1 bg-yellow-500 mt-2"></div>
                            </div>
                            
                            <div class="text-primary-400 text-2xl mx-4 hidden md:block">→</div>
                            
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mb-2">
                                    <i class="fas fa-trash text-white text-2xl"></i>
                                </div>
                                <span class="text-sm font-medium">DELETE</span>
                            </div>
                        </div>
                        
                        <!-- Gráfico de estadísticas -->
                        <div class="mt-8">
                            <h4 class="text-lg font-medium mb-4">Estadísticas de Operaciones</h4>
                            <div class="flex items-end h-32 space-x-2">
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-full bg-primary-500 rounded-t-lg" style="height: 80%"></div>
                                    <span class="mt-2 text-xs">Create</span>
                                </div>
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-full bg-green-500 rounded-t-lg" style="height: 100%"></div>
                                    <span class="mt-2 text-xs">Read</span>
                                </div>
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-full bg-yellow-500 rounded-t-lg" style="height: 60%"></div>
                                    <span class="mt-2 text-xs">Update</span>
                                </div>
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-full bg-red-500 rounded-t-lg" style="height: 30%"></div>
                                    <span class="mt-2 text-xs">Delete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instrucciones detalladas -->
                <div>
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-book-open mr-3 text-primary-400"></i>
                        Guía de Operaciones CRUD
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-primary-300 mb-2">CREATE (Crear)</h4>
                            <p class="text-sm text-gray-300">Añade nuevos registros a la base de datos. Todos los campos son obligatorios excepto los marcados como opcionales.</p>
                        </div>
                        
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-green-300 mb-2">READ (Consultar)</h4>
                            <p class="text-sm text-gray-300">Busca y visualiza información existente. Los campos se muestran en modo solo lectura.</p>
                        </div>
                        
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-yellow-300 mb-2">UPDATE (Actualizar)</h4>
                            <p class="text-sm text-gray-300">Modifica registros existentes. Primero debes buscar el registro por ID, luego editar los campos necesarios.</p>
                        </div>
                        
                        <div class="bg-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-red-300 mb-2">DELETE (Eliminar)</h4>
                            <p class="text-sm text-gray-300">Elimina registros de la base de datos. Esta acción requiere confirmación y no se puede deshacer.</p>
                        </div>
                        
                        <div class="bg-gray-900 rounded-lg p-4 mt-4">
                            <h4 class="font-medium text-white mb-2">Consejos:</h4>
                            <ul class="text-sm text-gray-400 space-y-1">
                                <li><i class="fas fa-check mr-2 text-green-400"></i> Verifica los datos antes de guardar</li>
                                <li><i class="fas fa-check mr-2 text-green-400"></i> Usa IDs válidos para operaciones de modificación</li>
                                <li><i class="fas fa-check mr-2 text-green-400"></i> Exporta datos periódicamente para respaldo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
                <p>Sistema CRUD desarrollado con Laravel y Tailwind CSS • © 2023 • <a href="#" class="text-primary-400 hover:text-primary-300">Documentación técnica</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Funcionalidad para cambiar entre pestañas CRUD
        const tabButtons = document.querySelectorAll('.tab-btn');
        const currentOperation = document.getElementById('current-operation');
        const operationDescription = document.getElementById('operation-description');
        const operationInstructions = document.getElementById('operation-instructions');
        const formStatus = document.getElementById('form-status');
        const idField = document.getElementById('id-field');
        const submitBtn = document.getElementById('submit-btn');
        const formInputs = document.querySelectorAll('#crud-form input, #crud-form select, #crud-form textarea');
        
        // Instrucciones para cada operación
        const operationData = {
            create: {
                name: "Crear (Create)",
                description: "En esta operación puedes agregar un nuevo producto a la base de datos. Completa todos los campos requeridos y haz clic en 'Guardar Producto'.",
                instructions: [
                    "Completa todos los campos del formulario",
                    "Asegúrate de que la información sea correcta",
                    "Haz clic en 'Guardar Producto' para crear el registro",
                    "Recibirás una confirmación cuando el proceso termine"
                ],
                status: "Listo para crear un nuevo producto",
                showIdField: false,
                buttonText: "Guardar Producto",
                buttonIcon: "fa-save"
            },
            read: {
                name: "Consultar (Read)",
                description: "En esta operación puedes buscar un producto por su ID para visualizar toda su información. Completa el campo ID y haz clic en 'Buscar'.",
                instructions: [
                    "Introduce el ID del producto que deseas consultar",
                    "Haz clic en el botón 'Buscar' para cargar la información",
                    "Los datos del producto aparecerán en los campos del formulario",
                    "Los campos estarán en modo solo lectura para esta operación"
                ],
                status: "Listo para realizar consultas",
                showIdField: true,
                buttonText: "Buscar Producto",
                buttonIcon: "fa-search"
            },
            update: {
                name: "Actualizar (Update)",
                description: "En esta operación puedes modificar la información de un producto existente. Primero busca el producto por ID, luego edita los campos necesarios.",
                instructions: [
                    "Introduce el ID del producto que deseas actualizar",
                    "Haz clic en 'Buscar' para cargar los datos actuales",
                    "Modifica los campos que necesites cambiar",
                    "Haz clic en 'Actualizar Producto' para guardar los cambios"
                ],
                status: "Listo para actualizar productos",
                showIdField: true,
                buttonText: "Actualizar Producto",
                buttonIcon: "fa-edit"
            },
            delete: {
                name: "Eliminar (Delete)",
                description: "En esta operación puedes eliminar un producto de la base de datos. Esta acción es irreversible, por lo que debes confirmar antes de proceder.",
                instructions: [
                    "Introduce el ID del producto que deseas eliminar",
                    "Haz clic en 'Buscar' para verificar la información",
                    "Revisa los datos del producto a eliminar",
                    "Confirma la eliminación en el siguiente paso"
                ],
                status: "Listo para eliminar productos (acción irreversible)",
                showIdField: true,
                buttonText: "Eliminar Producto",
                buttonIcon: "fa-trash"
            }
        };
        
        // Cambiar entre pestañas
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tab = button.getAttribute('data-tab');
                
                // Actualizar pestañas activas
                tabButtons.forEach(btn => {
                    btn.classList.remove('border-primary-500', 'text-primary-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                button.classList.remove('border-transparent', 'text-gray-500');
                button.classList.add('border-primary-500', 'text-primary-600');
                
                // Actualizar contenido según la operación
                updateOperationUI(tab);
            });
        });
        
        // Actualizar la UI según la operación seleccionada
        function updateOperationUI(operation) {
            const data = operationData[operation];
            
            // Actualizar textos
            currentOperation.textContent = data.name;
            operationDescription.textContent = data.description;
            formStatus.textContent = data.status;
            
            // Actualizar instrucciones
            operationInstructions.innerHTML = '';
            data.instructions.forEach(instruction => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fas fa-check-circle mr-2"></i> ${instruction}`;
                operationInstructions.appendChild(li);
            });
            
            // Mostrar/ocultar campo ID
            if (data.showIdField) {
                idField.classList.remove('hidden');
            } else {
                idField.classList.add('hidden');
            }
            
            // Actualizar botón de envío
            submitBtn.innerHTML = `<i class="fas ${data.buttonIcon} mr-2"></i> ${data.buttonText}`;
            
            // Cambiar color del botón según operación
            submitBtn.className = `px-6 py-2 text-white rounded-lg hover:opacity-90 transition font-medium`;
            
            if (operation === 'delete') {
                submitBtn.classList.add('bg-red-500', 'hover:bg-red-600');
            } else if (operation === 'read') {
                submitBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            } else if (operation === 'update') {
                submitBtn.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
            } else {
                submitBtn.classList.add('bg-primary-500', 'hover:bg-primary-600');
            }
            
            // Configurar campos como editables o de solo lectura
            const isReadOnly = operation === 'read';
            formInputs.forEach(input => {
                if (input.name !== 'product_id') {
                    input.readOnly = isReadOnly;
                    input.disabled = isReadOnly;
                    
                    if (isReadOnly) {
                        input.classList.add('bg-gray-100', 'cursor-not-allowed');
                        input.classList.remove('focus:ring-2', 'focus:ring-primary-500');
                    } else {
                        input.classList.remove('bg-gray-100', 'cursor-not-allowed');
                        input.classList.add('focus:ring-2', 'focus:ring-primary-500');
                    }
                }
            });
        }
        
        // Simular envío del formulario
        document.getElementById('crud-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener la operación actual
            const activeTab = document.querySelector('.tab-btn.border-primary-500').getAttribute('data-tab');
            const operationName = operationData[activeTab].name;
            
            // Mostrar mensaje de confirmación
            alert(`Operación "${operationName}" simulada. En una implementación real, los datos se enviarían al servidor.`);
            
            // Simular éxito
            formStatus.textContent = `Operación "${operationName}" completada con éxito`;
            formStatus.parentElement.classList.remove('bg-green-50', 'text-green-700');
            formStatus.parentElement.classList.add('bg-green-100', 'text-green-800');
            
            // Restaurar después de 3 segundos
            setTimeout(() => {
                formStatus.textContent = operationData[activeTab].status;
                formStatus.parentElement.classList.remove('bg-green-100', 'text-green-800');
                formStatus.parentElement.classList.add('bg-green-50', 'text-green-700');
            }, 3000);
        });
        
        // Botones de acción
        document.getElementById('btn-create').addEventListener('click', () => {
            updateOperationUI('create');
            
            // Activar la pestaña correspondiente
            tabButtons.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600');
                btn.classList.add('border-transparent', 'text-gray-500');
                
                if (btn.getAttribute('data-tab') === 'create') {
                    btn.classList.remove('border-transparent', 'text-gray-500');
                    btn.classList.add('border-primary-500', 'text-primary-600');
                }
            });
        });
        
        document.getElementById('btn-list').addEventListener('click', () => {
            alert('En una implementación real, aquí se mostraría una lista de todos los productos.');
        });
        
        // Inicializar con la operación READ (por defecto)
        updateOperationUI('read');
    </script>
</body>
</html>