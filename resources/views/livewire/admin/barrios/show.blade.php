
<div>
    <x-auth-header :title="__('Editar')" :description="__('Revisar los datos')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('Estado')" />

    <div class="body">  

 
        <form action="{{ route('barrios.index') }}" method="get" class="sky-form">

        <header class="ubuntu-bold-20-negro">Forma de Registro</header>
            
                @csrf

            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Identificacion GeoPis</label>
                        <div class="col col-8">
                            <strong>{{$barrio->id_DMQ}}</strongs>
                        </div>
                    </div>
                </section>
        <!-- barrio -->
                <section>
                    <div class="row">
                        <label class="label col col-4">Nombre del Barrio</label>
                        <div class="col col-8">
                            <strong>{{ $barrio->nombre }}</strong>
                        </div>
                    </div>
                </section>                
            </fieldset>

            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Sector</label>
                        <div class="col col-8">
                            <strong>{{ $barrio->sector }}</strong></strong>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <label class="label col col-4">Parroquia</label>
                        <div class="col col-8">
                            <strong>{{ $barrio->parroquia }}</strong>
                        </div>
                    </div>
                </section>
            </fieldset>
                

        <footer>
            <button type="submit" class="button text-black">Regresar</button>

        </footer>
            
    </form>
                    
    </div>
        
</div>
