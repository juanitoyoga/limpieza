<?php

namespace App\Livewire\Operacion\LogSistema;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.operacion')]
class Index extends Component
{
    public function mount()
    {
        Gate::authorize('log-sistema.ver');
    }

    public function render()
    {
        return view('livewire.operacion.log-sistema.index');
    }
}
