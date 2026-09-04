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
            <select wire:model.live="service_type_id" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($tiposDisponibles as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
            @error('service_type_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">
                Subtipo
                @unless($service_type_id)
                <span class="text-xs text-gray-400">(elige un tipo primero)</span>
                @endunless
            </label>
            <select wire:model.live="service_subtype_id" class="border rounded px-3 py-2 w-full"
                @unless($service_type_id) disabled @endunless>
                <option value="">Seleccione...</option>
                @foreach($subtiposDisponibles as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
            @error('service_subtype_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- 🆕 Informativo, no editable: el equipo viene del subtipo elegido --}}
        @if($service_subtype_id)
        <div class="bg-blue-50 border border-blue-200 rounded px-4 py-3">
            <p class="text-sm font-medium text-blue-900 mb-1">Equipo requerido por este subtipo:</p>
            @forelse($equipoDelSubtipo as $equipo)
            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                {{ $equipo->name }}
                @if($equipo->pivot->required)
                <i class="fas fa-asterisk text-[8px] ml-1" title="Requerido"></i>
                @endif
            </span>
            @empty
            <p class="text-xs text-gray-500">Este subtipo no tiene equipo configurado todavía.</p>
            @endforelse
        </div>
        @endif

        <div>
            <label class="block mb-1">Ámbito</label>
            <select wire:model="service_scope_id" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($ambitosDisponibles as $a)
                <option value="{{ $a->id }}">{{ $a->name }}</option>
                @endforeach
            </select>
            @error('service_scope_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Frecuencia</label>
            <select wire:model="frequency_id" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($frecuenciasDisponibles as $f)
                <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>
            @error('frequency_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block mb-1">Nivel de intervención</label>
            <select wire:model="intervention_level_id" class="border rounded px-3 py-2 w-full">
                <option value="">Seleccione...</option>
                @foreach($nivelesDisponibles as $n)
                <option value="{{ $n->id }}">{{ $n->name }}</option>
                @endforeach
            </select>
            @error('intervention_level_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1">Unidad de medida</label>
                <select wire:model="unit_id" class="border rounded px-3 py-2 w-full">
                    <option value="">Seleccione...</option>
                    @foreach($unidadesDisponibles as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
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