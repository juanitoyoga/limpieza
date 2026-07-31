@section('page-title', 'Catálogo de Servicios')
@section('page-description', 'Catálogo de servicios utilizados en resoluciones y ofertas')

<div class="p-6 max-w-2xl mx-auto">

    <h2 class="text-xl font-semibold mb-4">
        {{ $item ? 'Editar servicio' : 'Nuevo servicio' }}
    </h2>

    @if (session('message'))
    <div class="bg-green-50 text-green-700 border border-green-200 rounded px-3 py-2 mb-4 text-sm">
        {{ session('message') }}
    </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1">Código</label>
                <input type="text" wire:model="codigo"
                    class="border rounded px-3 py-2 w-full"
                    placeholder="Se autogenera si se deja vacío">
                @error('codigo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-1">Nombre *</label>
                <input type="text" wire:model="nombre" class="border rounded px-3 py-2 w-full">
                @error('nombre') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block mb-1">Descripción</label>
            <textarea wire:model="descripcion" rows="2" class="border rounded px-3 py-2 w-full"></textarea>
            @error('descripcion') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Tipo *</label>
            <select wire:model="tipo" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($tipos as $t)
                <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
            @error('tipo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Subtipo</label>
            <select wire:model="subtipo" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($subtipos as $s)
                <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Ámbito</label>
            <select wire:model="ambito" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($ambitos as $a)
                <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Frecuencia</label>
            <select wire:model="frecuencia" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($frecuencias as $f)
                <option value="{{ $f }}">{{ $f }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Nivel de intervención</label>
            <select wire:model="nivel_intervencion" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($niveles as $n)
                <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1">Equipamiento</label>
            <select wire:model="equipamiento" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($equipos as $e)
                <option value="{{ $e }}">{{ $e }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1">Unidad de medida</label>
                <select wire:model="unidad_medida" class="border rounded px-3 py-2 w-full">
                    <option value="">Seleccione...</option>
                    @foreach($unidades as $u)
                    <option value="{{ $u }}">{{ $u }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1">Costo referencial (USD)</label>
                <input type="number" step="0.01" min="0" wire:model="costo_referencial"
                    class="border rounded px-3 py-2 w-full">
                @error('costo_referencial') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-1">Orden</label>
                <input type="number" min="0" wire:model="orden" class="border rounded px-3 py-2 w-full">
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <input type="checkbox" wire:model="estado">
            <label>Activo</label>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('catalogo-servicios.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
            <button class="bg-green-600 text-white px-4 py-2 rounded">
                Guardar
            </button>
        </div>
    </form>
</div>