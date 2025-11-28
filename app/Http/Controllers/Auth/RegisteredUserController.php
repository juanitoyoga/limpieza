<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

use Illuminate\Support\Facades\Mail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {


        $request->validate([
            'tipo_id' => ['required', 'string', 'max:25'],
            'nro_id' => ['required', 'string', 'max:25'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
                

        $user = User::create([
            'nro_id' => $request->nro_id,
            'tipo_id' => $request->tipo_id,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => $request->password, // ya se hashea por el mutator
        ]);
        

        event(new Registered($user));

        Auth::login($user);
        
        
        Mail::to($user->email)->send(new \App\Mail\WelcomeUserMail($user));

        return redirect()->route('welcome')
        ->with('success', '¡Registro completado! Bienvenido a la aplicación, tu participación fortalece la comunidad.');
    

    }
}
