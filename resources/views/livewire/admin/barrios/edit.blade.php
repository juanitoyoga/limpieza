
@section('page-title', 'Barrios')
    
@section('page-description', 'Actualizacion datos del Barrio')

@section('content')

<div x-data="{ scroll: true }" class="p-4 sm:p-6 bg-white shadow rounded">

    <div class="body">  
    <form wire:submit.prevent="update" id="sky-form" class="sky-form">
 

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
                

        <footer>
            <button type="submit" class="button">Actualizar</button>

        </footer>
            
    </form>
                    
    </div>
        
</div>
