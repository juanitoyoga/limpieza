@php
    use Illuminate\Support\Str;
@endphp

<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">

    <x-auth-header 
        :title="__('Registrar')" 
        :description="__('Debajo ingrese el Salario Minimo')" 
    />

    <x-auth-session-status class="text-center" :status="session('Estado')" />

    <form wire:submit.prevent="store" class="sky-form">

        <header class="ubuntu-bold-20-negro">Forma de Registro</header>

        {{-- ====================== --}}
        {{-- CÓDIGO DE CONTRAVENCIÓN --}}
        {{-- ====================== --}}
        <fieldset>
            <section>
                <div class="row">
                    <label class="label col col-4">Código de la Contravención</label>
                    <div class="col col-8">
                        
                        <label class="select">
                            <flux:select wire:model="ordenanza_id">
                                <flux:select.option value="">Seleccione...</flux:select.option>
                                @foreach($ordenanzas as $item)
                                    <flux:select.option value="{{ $item->id }}">
                                        {{ $item->codigo }} — {{ Str::limit($item->descripcion, 50) }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </label>

                        @error('ordenanza_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror

                    </div>
                </div>
            </section>
        </fieldset>

        {{-- ====================== --}}
        {{-- AÑO / SALARIO MÍNIMO   --}}
        {{-- ====================== --}}
        <fieldset>
            <section>
                <div class="row">
                    <label class="label col col-4">Valor Salario</label>
                    <div class="col col-8">

                        <label class="select">
                            <flux:select wire:model="salario_id">
                                <flux:select.option value="">Seleccione...</flux:select.option>
                                @foreach($salarios as $salario)
                                    <flux:select.option value="{{ $salario->id }}">
                                        {{ $salario->year }} — {{ $salario->valor_usd }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </label>

                        @error('salario_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror

                    </div>
                </div>
            </section>
        </fieldset>

        {{-- ====================== --}}
        {{-- PORCENTAJE             --}}
        {{-- ====================== --}}
        <fieldset>
            <section>
                <div class="row">
                    <label class="label col col-4">Porcentaje (%)</label>
                    <div class="col col-8">

                        <input 
                            type="number" 
                            step="0.01" 
                            wire:model="porcentaje"
                            class="w-full border p-2 rounded"
                        >

                        @error('porcentaje')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror

                    </div>
                </div>
            </section>
        </fieldset>

        {{-- ====================== --}}
        {{-- BOTÓN SUBMIT           --}}
        {{-- ====================== --}}
        <footer class="crud-footer">
            <button 
                type="submit" 
                class="btn btn-reverse btn-lime"
            >
                Registrar
            </button>
        </footer>

    </form>
    
</div>
