<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class Welcome extends Component
{
    public function render()
    {
        return view('livewire.public.welcome');
    }
}
