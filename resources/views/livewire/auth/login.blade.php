<div class="body body-s">		
     
            <form  method="POST" action="{{ route('login') }}" id="sky-form" class="sky-form">
                <header class="ubuntu-bold-20-negro">Forma de Ingreso</header>
                    
                        @csrf


                <fieldset>                  

                        <section>
                            <div class="row">
                                <label class="label col col-4">Corrreo-E</label>
                                <div class="col col-8">
                                    <label class="input">
                                        <i class="icon-append fa fa-user"></i>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </label>
                                </div>
                            </div>
                        </section>                                             
                        <section>
                            <div class="row">
                                <label class="label col col-4">Contraseña</label>
                                <div class="col col-8">
                                    <label class="input">
                                        <i class="icon-append fa fa-lock"></i>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </label>
                                    <div class="note"><a href="#sky-form2" class="modal-opener">Se olvido contraseña?</a></div>
                                </div>
                            </div>
                        </section>
                        
                        <section>
                            <div class="row">
                                <div class="col col-4"></div>
                                <div class="col col-8">
                                    <label class="checkbox"><input type="checkbox" name="remember" checked><i></i>Mantenerme Ingresado</label>
                                </div>
                            </div>
                        </section>

                </fieldset>

           <footer class="crud-footer">
                <button type="submit" class="btn btn-reverse btn-lime">
                    Ingresar
                </button>

                <button type="button" class="btn btn-reverse btn-teal" onclick="window.location='{{ route('register') }}'">
                    Registrarse
                </button>

            </footer>
                
            </form>                

        </div>
 
