@section('page-title', 'Editar Nominación')
@section('page-description', 'Actualización de una nominación existente')

<div>
    {{-- Mensaje de éxito --}}
    @if(session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow border overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-x">

            {{-- FORMULARIO --}}
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">
                    <i class="fas fa-edit mr-2 text-blue-500"></i>
                    Editar Nominación
                </h2>

                <form wire:submit.prevent="save" class="space-y-6">

                    {{-- (TODOS LOS CAMPOS SON IGUALES A CREATE) --}}

                    {{-- PDF --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Documento PDF</label>

                        @if($nomination->pdf_path)
                            <p class="text-sm mb-2">
                                <a href="{{ Storage::url($nomination->pdf_path) }}"
                                   target="_blank"
                                   class="text-blue-600 underline">
                                    <i class="fas fa-file-pdf mr-1"></i> Ver PDF actual
                                </a>
                            </p>
                        @endif

                        <input type="file" wire:model="pdf" accept="application/pdf"
                               class="w-full px-4 py-3 border rounded-lg">
                        @error('pdf') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- BOTÓN --}}
                    <div class="pt-6 border-t">
                        <button type="submit"
                                class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg">
                            <i class="fas fa-save mr-2"></i>
                            Actualizar Nominación
                        </button>
                    </div>

                </form>
            </div>

            {{-- PANEL --}}
            <div class="p-6 bg-gray-50">
                <div class="text-center">
                    <i class="fas fa-pen-fancy text-4xl text-blue-500 mb-4"></i>
                    <h3 class="text-xl font-semibold">Edición Controlada</h3>
                    <p class="text-gray-600">
                        Toda modificación queda registrada en auditoría.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
