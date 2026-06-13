@section('page-title', 'Detalle del Vecino')
@section('page-description', 'Información completa del registro')

<div class="max-w-4xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 1 1 15 0v.75H4.5v-.75Z" />
            </svg>
            Detalle del Vecino
        </h2>

        {{-- Datos del Usuario --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Datos del Usuario</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nombre</p>
                    <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium text-gray-800">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Cédula</p>
                    <p class="font-medium text-gray-800">{{ $vecino->cedula }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Teléfono</p>
                    <p class="font-medium text-gray-800">{{ $vecino->telefono ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Dirección --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Dirección</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Calle Principal</p>
                    <p class="font-medium text-gray-800">{{ $vecino->calle_principal }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Número</p>
                    <p class="font-medium text-gray-800">{{ $vecino->numero }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Calle Secundaria</p>
                    <p class="font-medium text-gray-800">{{ $vecino->calle_secundaria }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Referencias</p>
                    <p class="font-medium text-gray-800">{{ $vecino->referencias ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Barrio --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Barrio</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nombre</p>
                    <p class="font-medium text-gray-800">{{ $barrio->nombre }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Código GeoPis</p>
                    <p class="font-medium text-gray-800">{{ $barrio->id_DMQ }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Parroquia</p>
                    <p class="font-medium text-gray-800">{{ $barrio->parroquia }}</p>
                </div>
            </div>
        </div>

        {{-- Ocupación / Deportes / Recreación --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Información Personal</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Ocupación --}}
                <div>
                    <p class="text-sm text-gray-500 mb-1">Ocupación</p>
                    @forelse($vecino->ocupacion ?? [] as $item)
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full mr-1 mb-1">
                        {{ $item }}
                    </span>
                    @empty
                    <p class="text-gray-400 text-sm">Sin datos</p>
                    @endforelse
                </div>

                {{-- Deportes --}}
                <div>
                    <p class="text-sm text-gray-500 mb-1">Deportes</p>
                    @forelse($vecino->deportes ?? [] as $item)
                    <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full mr-1 mb-1">
                        {{ $item }}
                    </span>
                    @empty
                    <p class="text-gray-400 text-sm">Sin datos</p>
                    @endforelse
                </div>

                {{-- Recreación --}}
                <div>
                    <p class="text-sm text-gray-500 mb-1">Recreación / Hobbies</p>
                    @forelse($vecino->recreacion ?? [] as $item)
                    <span class="inline-block px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full mr-1 mb-1">
                        {{ $item }}
                    </span>
                    @empty
                    <p class="text-gray-400 text-sm">Sin datos</p>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- Botón volver --}}
        <div class="pt-4 border-t border-gray-200 flex justify-end">
            <a href="{{ route('vecinos.lista') }}"
                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                Volver
            </a>
        </div>

    </div>
</div>