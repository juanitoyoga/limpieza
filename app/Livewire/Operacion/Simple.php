<?php

namespace App\Livewire\Operacion;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Simple extends Component
{
    public function render()
    {
        return view('livewire.operacion.simple');
    }
}
