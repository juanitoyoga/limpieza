<?php

namespace App\Livewire\Auth;

use Livewire\Component;

use Livewire\Attributes\Layout;

class Register extends Component
{

    #[Layout('layouts.operacion')]

    public function render()
    {
        return view('livewire.auth.register');
    }
}

