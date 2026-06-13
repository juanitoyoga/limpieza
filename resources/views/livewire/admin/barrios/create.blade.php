@extends('layouts.admin')
@section('page-title', 'Nuevo Barrio')
@section('page-description', 'Complete el formulario y dibuje el área del barrio en el mapa')
@section('content')

<div class="grid grid-cols-1 gap-6">


    <h1 class="text-2xl font-bold mb-6">Registrar Nuevo Barrio</h1>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
    <div class="p-4 mb-4 bg-green-100 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
    @endif


    {{-- Errores --}}
    @if($errors->any())
    <div class="p-4 mb-4 bg-red-100 text-red-800 rounded-lg">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('barrios.store') }}" method="POST">
        @csrf



        {{-- FORMULARIO --}}
        <div class="bg-white p-6 rounded-lg shadow">

            <h2 class="text-lg font-semibold mb-4">Información del Barrio</h2>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Identificación GeoPis *</label>
                <input type="text" name="id_DMQ" class="w-full border rounded px-3 py-2"
                    value="{{ old('id_DMQ') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nombre del Barrio *</label>
                <input type="text" name="nombre" class="w-full border rounded px-3 py-2"
                    value="{{ old('nombre') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Sector *</label>
                <input type="text" name="sector" class="w-full border rounded px-3 py-2"
                    value="{{ old('sector') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Parroquia *</label>
                <input type="text" name="parroquia" class="w-full border rounded px-3 py-2"
                    value="{{ old('parroquia') }}" required>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Latitud centroide</label>
                    <input type="text" id="input-lat" name="lat" readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Longitud centroide</label>
                    <input type="text" id="input-lng" name="lng" readonly
                        class="w-full border rounded px-3 py-2 bg-gray-100">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Polígono</label>
                <div id="polygon-info" class="text-sm text-gray-600 bg-gray-100 p-3 rounded">
                    Sin polígono dibujado
                </div>
            </div>

            {{-- INPUT OCULTO PARA EL POLÍGONO --}}
            <input type="hidden" name="poligono" id="input-poligono">

            <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                Guardar Barrio
            </button>

        </div>

        {{-- MAPA --}}
        <div class="bg-white p-6 rounded-lg shadow">

            <h2 class="text-lg font-semibold mb-4">Mapa del Barrio</h2>

            <p class="text-sm text-gray-500 mb-2">
                Haz clic para agregar puntos.
            </p>
            <div class="flex gap-3 mb-4">
                <button id="closePolygonBtn"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Cerrar polígono
                </button>

                <button id="resetDrawingBtn"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Reiniciar dibujo
                </button>
            </div>

            <div id="map" style="width:100%; height:480px; background:red;"></div>


        </div>

    </form>

</div>

@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}"></script>
<script src="{{ asset('js/mapas/barrio-create.js') }}"></script>
@endsection