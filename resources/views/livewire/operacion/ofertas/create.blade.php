@section('page-title', 'Ofertas de Servicios')
@section('page-description', 'Gestión de ofertas de servicios asociadas a resoluciones')
<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Crear Oferta de Servicios</h1>
        </div>

        @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('ofertas.store') }}" method="POST">
            @include('livewire.operacion.ofertas.form')

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('ofertas.index') }}" class="px-4 py-2 border rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow">Guardar Oferta</button>
            </div>
        </form>
    </div>
</x-app-layout>