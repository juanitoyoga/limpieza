<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">

    <x-auth-header 
        :title="__('Registrar')" 
        :description="__('Debajo ingrese el Salario Minimo')" 
    />

    <x-auth-session-status class="text-center" :status="session('Estado')" />

    <div class="body">
        <form wire:submit.prevent="store" id="sky-form" class="sky-form">

            <header class="ubuntu-bold-20-negro">Forma de Registro</header>

            @csrf

            {{-- ========================= --}}
            {{-- CAMPO: YEAR             --}}
            {{-- ========================= --}}
            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Año Vigencia</label>
                        <div class="col col-8">
                            <label class="input">
                                <flux:input
                                    wire:model="year"
                                    type="number"
                                    required
                                    autocomplete="year"
                                    :placeholder="__('Año Vigencia')"
                                />
                            </label>

                            @error('year')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>
            </fieldset>


            {{-- ========================= --}}
            {{-- CAMPO: VALOR USD          --}}
            {{-- ========================= --}}
            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Valor Salario</label>
                        <div class="col col-8">
                            <label class="input">
                                <flux:input 
                                    wire:model="valor_usd"
                                    type="number"
                                    required
                                    placeholder="Valor en dolares"

                                />
                            </label>

                            @error('valor_usd')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

            </fieldset>


            {{-- ========================= --}}
            {{-- BOTÓN SUBMIT               --}}
            {{-- ========================= --}}
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
</div>
