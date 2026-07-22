@extends('layouts.admin')
@section('page-title', 'Mostrar Barrio - Atributo')
@section('page-description', 'Revisar plazo de justificación')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">
                <i class="fas fa-eye mr-2 text-blue-500"></i>Detalle de Barrio - Atributo
            </h2>
            <p class="text-sm text-gray-500">Vista de solo lectura para la revisión de parámetros.</p>
        </div>

        <div class="space-y-4">
            {{-- Barrio (Solo lectura / Input simple) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barrio</label>
                <input readonly
                    value="{{ $barrioAtributo->barrio?->nombre ?? 'Sin barrio asignado' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed transition" />
            </div>

            {{-- Contravención / Ordenanza (Solo lectura / Textarea para textos largos) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contravención (Ordenanza 332)</label>
                <textarea readonly rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed transition resize-none"
                    placeholder="Sin descripción registrada">{{ $barrioAtributo->ordenanza?->descripcion ?? 'Sin descripción registrada' }}</textarea>
            </div>

            {{-- Plazo de Justificación --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Plazo de justificación (horas)</label>
                <input readonly
                    value="{{ $barrioAtributo->plazo_horas ?? 'No requiere justificación' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed transition" />
            </div>

            {{-- Número de convenio --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nro. de convenio</label>
                <input readonly
                    value="{{ $barrioAtributo->nro_convenio ?? 'Sin convenio registrado' }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed transition" />
            </div>

            {{-- Botón de navegación --}}
            <div class="pt-4 border-t border-gray-200 flex justify-end">
                <a href="{{ route('barrio-atributo.lista') }}"
                    class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white font-medium rounded-lg transition inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Volver a la Lista
                </a>
            </div>

        </div>
    </div>
</div>
@endsection