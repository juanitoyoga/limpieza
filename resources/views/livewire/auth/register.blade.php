<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $nro_id = '';
    public string $tipo_id = '';
    public string $first_name = '';
    public string $last_name = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'tipo_id' => ['required', 'string'],
            'nro_id' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:55'],
            'first_name' => ['required', 'string', 'max:55'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);  

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <x-auth-header :title="__('Create an account')" :description="__('Debajo ingrese sus datos para registrarse')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('Estado')" />

    <div class="body">   
    <form  method="POST" action="{{ route('register') }}" id="sky-form" class="sky-form">
        <header class="ubuntu-bold-20-negro">Forma de Registro</header>
            
                @csrf

            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Documento</label>
                        <div class="col col-8">
                            <label class="select">                        
                                <flux:select wire:model="tipo_id" placeholder="Seleccione...">
                                    <flux:select.option>Cedula</flux:select.option>
                                    <flux:select.option>Pasaporte</flux:select.option>
                                    <flux:select.option>RUC</flux:select.option>
                                </flux:select>
                            </label>
                        </div>
                    </div>
                </section>
        <!-- Numero ID -->
                <section>
                    <div class="row">
                        <label class="label col col-4">Numero ID</label>
                        <div class="col col-8">
                            <label class="input">
                                <flux:input
                                wire:model="nro_id"
                                type="text"
                                required
                                autocomplete="nro_id"
                                :placeholder="__('Nro. de identificación')"
                            />
                    
                            </label>
                        </div>
                    </div>
                </section>                
            </fieldset>

            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Nombres</label>
                        <div class="col col-8">
                            <label class="input">
                        <!-- nombres -->
                                <flux:input
                                    wire:model="first_name"
                                    type="text"
                                    required
                                    autocomplete="first_name"
                                    :placeholder="__('Nombres Completos')"
                                />

                            </label>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <label class="label col col-4">Apellidos</label>
                        <div class="col col-8">
                            <label class="input">
                                    <!-- Apellidos -->
                                    <flux:input
                                        wire:model="last_name"
                                        type="text"
                                        required
                                        autocomplete="last_name"
                                        :placeholder="__('Apellidos')"
                                    />

                            </label>
                        </div>
                    </div>
                </section>
            </fieldset>
             
            <fieldset>
                <section>
                    <div class="row">
                        <label class="label col col-4">Correo Electronico</label>
                        <div class="col col-8">
                            <label class="input">
                            <!-- Email Address -->
                            <flux:input
                                wire:model="email"
                                type="email"
                                required
                                autocomplete="email"
                                placeholder="email@example.com"
                            />

                            </label>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <label class="label col col-4">Contraseña</label>
                        <div class="col col-8">
                            <label class="input">
                        <!-- Password -->
                        <flux:input
                            wire:model="password"
                            type="password"
                            required
                            autocomplete="new-password"
                            :placeholder="__('Contraseña')"
                        />

                            </label>                
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
                        <label class="label col col-4">Repetir Contraseña</label>
                        <div class="col col-8">
                            <label class="input">
                        <!-- Confirm Password -->
                        <flux:input
                            wire:model="password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            :placeholder="__('Repetir Contraseña')"
                            />
                                     
                            </label>                
                        </div>
                    </div>
                </section>
            </fieldset>
            <footer class="crud-footer">
                <button type="submit" class="btn btn-reverse btn-lime">
                    Registrarse
                </button>

                <button type="button" class="btn btn-reverse btn-teal" onclick="window.location='{{ route('login') }}'">
                    Usuario Registrado Ingresar
                </button>

            </footer>
            
          
    </form>
                  
    </div>

</div>
