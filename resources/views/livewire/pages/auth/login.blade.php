@include('partials._main');
<body>


@include('partials._header');


        <div class="body body-s bg-blue_1">   
            <form  method="POST" action="{{ route('login') }}" id="sky-form" class="sky-form">
                <header class="ubuntu-bold-20-negro">Login form</header>
                    
                        @csrf


                <fieldset>                  
                    <section>
                        <div class="row">
                            <label for="email" class="col-md-4 col-form-label text-md-end ubuntu-bold-14-negro ">{{ __('Email Address') }}</label>
                            <div class="col col-md-8">

                                    <i class="icon-append fa fa-user"></i>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="ubuntu-bold-14-negro">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </section>                        

                    <section>>
                        <div class="row">
                            <label for="password" class="col-md-4 col-form-label text-md-end ubuntu-bold-14-negro">{{ __('Password') }}</label>

                            <div class="col-md-8">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </section>
                </fieldset>

                <fieldset>
                    <section>


                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label ubuntu-bold-14-negro" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </section>
            </fieldset>

        </div>
 

            @include('partials._footer');
</body>
</html>