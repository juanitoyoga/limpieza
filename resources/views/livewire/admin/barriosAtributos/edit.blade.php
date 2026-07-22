@extends('layouts.admin')
@section('page-title', 'Editar Barrio - Atributo')
@section('page-description', 'Actualizar plazo de justificación')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">
                <i class="fas fa-pen mr-2 text-yellow-500"></i>Editar Barrio - Atributo
            </h2>
        </div>

        @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <ul class="text-sm text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('barrio-atributo.update', $barrioAtributo->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio *</label>
                <select name="barrio_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    @foreach ($barrios as $barrio)
                    <option value="{{ $barrio->id }}" {{ old('barrio_id', $barrioAtributo->barrio_id) == $barrio->id ? 'selected' : '' }}>
                        {{ $barrio->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contravención (Ordenanza 332) *</label>
                <select name="ordenanza332_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 transition">
                    @foreach ($ordenanzas as $ordenanza)
                    <option value="{{ $ordenanza->id }}" {{ old('ordenanza332_id', $barrioAtributo->ordenanza332_id) == $ordenanza->id ? 'selected' : '' }}>
                        {{ $ordenanza->descripcion }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plazo de justificación (horas)</label>
                <input type="number" name="plazo_horas" min="1"
                    value="{{ old('plazo_horas', $barrioAtributo->plazo_horas) }}"
                    placeholder="Déjalo vacío si no requiere justificación"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nro. de convenio</label>
                <input type="string" name="nro_convenio"
                    value="{{ old('nro_convenio', $barrioAtributo->nro_convenio) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            </div>

            <div class="pt-4 border-t border-gray-200 flex gap-3">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Actualizar
                </button>
                <a href="{{ route('barrio-atributo.lista') }}"
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</div>
@endsection