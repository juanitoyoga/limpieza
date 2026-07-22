@extends('layouts.admin')
@section('page-title', 'Barrios - Atributos')
@section('page-description', 'Plazos de justificación por barrio y contravención')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">
                <i class="fas fa-search mr-2 text-blue-500"></i>Buscar Barrio - Atributo
            </h2>
            <p class="text-sm text-gray-500">
                Completa uno o más filtros y presiona Buscar.
            </p>
        </div>

        <form action="{{ route('barrio-atributo.lista') }}" method="GET" class="space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                <select name="barrio_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Todos</option>
                    @foreach ($barrios as $barrio)
                    <option value="{{ $barrio->id }}" {{ old('barrio_id') == $barrio->id ? 'selected' : '' }}>
                        {{ $barrio->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contravención (Ordenanza 332)</label>
                <select name="ordenanza332_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">Todas</option>
                    @foreach ($ordenanzas as $ordenanza)
                    <option value="{{ $ordenanza->id }}" {{ old('ordenanza332_id') == $ordenanza->id ? 'selected' : '' }}>
                        {{ $ordenanza->descripcion }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4 border-t border-gray-200 flex gap-3">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
                <a href="{{ route('barrio-atributo.create') }}"
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Nuevo
                </a>
            </div>

        </form>
    </div>
</div>
@endsection