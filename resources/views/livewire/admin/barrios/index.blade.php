@extends('layouts.admin')
@section('page-title', 'Barrios')
@section('page-description', 'Búsqueda y filtros')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">
                <i class="fas fa-search mr-2 text-blue-500"></i>Buscar Barrios
            </h2>
            <p class="text-sm text-gray-500">
                Completa uno o más filtros y presiona Buscar.
            </p>
        </div>

        <form action="{{ route('barrios.lista') }}" method="GET" class="space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Código GeoPis
                </label>
                <input type="text" name="id_DMQ"
                    value="{{ old('id_DMQ') }}"
                    placeholder="Ej: DMQ-001"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre del Barrio
                </label>
                <input type="text" name="nombre"
                    value="{{ old('nombre') }}"
                    placeholder="Ej: Centro Histórico"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Sector
                </label>
                <input type="text" name="sector"
                    value="{{ old('sector') }}"
                    placeholder="Ej: Norte"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Parroquia
                </label>
                <input type="text" name="parroquia"
                    value="{{ old('parroquia') }}"
                    placeholder="Ej: San Sebastián"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Estado
                </label>
                <select name="activo"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Todos</option>
                    <option value="1" {{ old('activo') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ old('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="pt-4 border-t border-gray-200 flex gap-3">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
                <a href="{{ route('barrios.create') }}"
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Nuevo
                </a>
            </div>

        </form>
    </div>
</div>
@endsection