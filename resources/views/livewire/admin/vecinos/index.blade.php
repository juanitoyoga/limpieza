@extends('layouts.admin')
@section('page-title', 'Vecinos')
@section('page-description', 'Búsqueda y filtros')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">
                <i class="fas fa-search mr-2 text-blue-500"></i>Buscar Vecinos
            </h2>
            <p class="text-sm text-gray-500">Completa uno o más filtros y presiona Buscar.</p>
        </div>

        <form action="{{ route('vecinos.lista') }}" method="GET" class="space-y-4">

            {{-- Cédula del vecino --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                <input type="text" name="cedula"
                    value="{{ request('cedula') }}"
                    placeholder="Ej: 1712345678"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            {{-- Nombre del usuario --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del usuario</label>
                <input type="text" name="nombre"
                    value="{{ request('nombre') }}"
                    placeholder="Ej: Juan Pérez"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            {{-- Email del usuario --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="text" name="email"
                    value="{{ request('email') }}"
                    placeholder="Ej: juan@example.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            {{-- Código DMQ --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código GeoPis (Barrio)</label>
                <input type="text" name="id_DMQ"
                    value="{{ request('id_DMQ') }}"
                    placeholder="Ej: DMQ-001"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            {{-- Nombre del barrio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                <input type="text" name="barrio"
                    value="{{ request('barrio') }}"
                    placeholder="Ej: San José"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            {{-- Parroquia --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parroquia</label>
                <input type="text" name="parroquia"
                    value="{{ request('parroquia') }}"
                    placeholder="Ej: San Sebastián"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            {{-- Estado del vecino --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="activo"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Todos</option>
                    <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <button type="submit"
                    class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>

        </form>
    </div>
</div>
@endsection