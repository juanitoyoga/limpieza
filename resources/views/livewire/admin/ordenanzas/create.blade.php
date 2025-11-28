<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">

    <x-auth-header 
        :title="__('Registrar')" 
        :description="__('Debajo ingrese la nueva contravención')" 
    />

    <x-auth-session-status class="text-center" :status="session('Estado')" />

    <div class="body">
        <form wire:submit.prevent="store" id="sky-form" class="sky-form">

            <header class="ubuntu-bold-20-negro">Forma de Registro</header>

            @csrf

            {{-- ========================= --}}
            {{-- CAMPO: CÓDIGO             --}}
            {{-- ========================= --}}
            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Código</label>
                        <div class="col col-8">
                            <label class="input">
                                <flux:input
                                    wire:model="codigo"
                                    type="text"
                                    required
                                    autocomplete="codigo"
                                    :placeholder="__('Código')"
                                />
                            </label>

                            @error('codigo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ========================= --}}
                {{-- CAMPO: TIPO               --}}
                {{-- ========================= --}}
                <section>
                    <div class="row">
                        <label class="label col col-4">Tipo</label>
                        <div class="col col-8">
                            <label class="select">
                                <flux:select wire:model="tipo">
                                    <flux:select.option value="">Seleccione...</flux:select.option>
                                    <flux:select.option value="Primera Clase">Primera Clase</flux:select.option>
                                    <flux:select.option value="Segunda Clase">Segunda Clase</flux:select.option>
                                    <flux:select.option value="Tercera Clase">Tercera Clase</flux:select.option>
                                </flux:select>
                            </label>

                            @error('tipo')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>
            </fieldset>


            {{-- ========================= --}}
            {{-- CAMPO: DESCRIPCIÓN         --}}
            {{-- ========================= --}}
            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Descripción</label>
                        <div class="col col-8">
                            <label class="input">
                                <textarea 
                                    wire:model="descripcion"
                                    required
                                    placeholder="Escribe aquí la descripción..."
                                    rows="3"
                                    class="w-full border rounded px-3 py-2"
                                ></textarea>
                            </label>

                            @error('descripcion')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ========================= --}}
                {{-- CAMPO: NIVEL DE GRAVEDAD  --}}
                {{-- ========================= --}}
                <section>
                    <div class="row">
                        <label class="label col col-4">Nivel de Gravedad</label>
                        <div class="col col-8">
                            <label class="select">
                                <flux:select wire:model="nivel_gravedad">
                                    <flux:select.option value="">Seleccione...</flux:select.option>
                                    <flux:select.option value="Leve">Leve</flux:select.option>
                                    <flux:select.option value="Medio">Medio</flux:select.option>
                                    <flux:select.option value="Grave">Grave</flux:select.option>
                                </flux:select>
                            </label>

                            @error('nivel_gravedad')
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
