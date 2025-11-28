<?php

namespace App\Livewire\Admin\Barrios;

use Livewire\Component;

use App\Models\Barrio;

use Livewire\Attributes\Layout;


#[Layout('layouts.admin')]
class Show extends Component
{
    public $barrio;

    // Laravel inyecta el modelo automáticamente
    public function mount(Barrio $barrio)
    {
        $this->barrio = $barrio;
    }

    public function render()
    {
        return view('livewire.admin.barrios.show');
    }
}
