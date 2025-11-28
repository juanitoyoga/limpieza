
<div>
    <x-auth-header :title="__('Registrar')" :description="__('Debajo ingrese el nuevo barrio')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('Estado')" />

    <div class="body">  
    <form wire:submit.prevent="store" id="sky-form" class="sky-form">
 

        <header class="ubuntu-bold-20-negro">Forma de Registro</header>
            
                @csrf

            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Identificacion GeoPis</label>
                        <div class="col col-8">
                            <label class="input">
                                <flux:input
                                wire:model="id_DMQ"
                                type="text"
                                required
                                autocomplete="id_DMQ"
                                :placeholder="__('Identificación GeoPis')"
                            />
                    
                            </label>
                        </div>
                    </div>
                </section>
        <!-- barrio -->
                <section>
                    <div class="row">
                        <label class="label col col-4">Nombre del Barrio</label>
                        <div class="col col-8">
                            <label class="input">
                                <flux:input
                                wire:model="nombre"
                                type="text"
                                required
                                autocomplete="nombre"
                                :placeholder="__('Nombre del Barrio')"
                            />
                    
                            </label>
                        </div>
                    </div>
                </section>                
            </fieldset>

            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Sector</label>
                        <div class="col col-8">
                            <label class="input">
                        <!-- nombres -->
                                <flux:input
                                    wire:model="sector"
                                    type="text"
                                    required
                                    autocomplete="sector"
                                    :placeholder="__('Sector')"
                                />

                            </label>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <label class="label col col-4">Parroquia</label>
                        <div class="col col-8">
                            <label class="input">
                                    <!-- parroquia -->
                                    <flux:input
                                        wire:model="parroquia"
                                        type="text"
                                        required
                                        autocomplete="parroquia"
                                        :placeholder="__('Parroquia')"
                                    />

                            </label>
                        </div>
                    </div>
                </section>
            </fieldset>
                

           <footer class="crud-footer">
                <button type="submit" class="btn btn-reverse btn-lime">
                    Registrar
                </button>
            </footer>
            
    </form>
                    
    </div>
        
</div>
