<div class="bg-white p-6 rounded-lg shadow">

    <div class="flex justify-between mb-4">
        <h2 class="text-lg font-semibold">Resultados</h2>

        <a href="{{ route('barrios.create') }}"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Crear nuevo barrio
        </a>
    </div>

    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sector</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parroquia</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            @forelse($barrios as $barrio)
            <tr>
                <td class="px-6 py-4">{{ $barrio->id_DMQ }}</td>
                <td class="px-6 py-4">{{ $barrio->nombre }}</td>
                <td class="px-6 py-4">{{ $barrio->sector }}</td>
                <td class="px-6 py-4">{{ $barrio->parroquia }}</td>
                <td class="px-6 py-4 text-center flex justify-center gap-3">

                    <a href="{{ route('barrios.show', $barrio->id) }}"
                        class="text-blue-600 hover:underline">Ver</a>

                    <a href="{{ route('barrios.edit', $barrio->id) }}"
                        class="text-yellow-600 hover:underline">Editar</a>

                    <form action="{{ route('barrios.destroy', $barrio->id) }}"
                        method="POST" onsubmit="return confirm('¿Eliminar barrio?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:underline">Eliminar</button>
                    </form>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                    No se encontraron resultados.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $barrios->links() }}
    </div>

</div>