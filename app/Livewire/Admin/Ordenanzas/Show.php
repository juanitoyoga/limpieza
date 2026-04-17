<?php

namespace App\Livewire\Admin\Ordenanzas;

use Livewire\Component;

use App\Models\Ordenanza332;

use Livewire\Attributes\Layout;


#[Layout('layouts.admin')]
class Show extends Component
{
    public $ordenanza;

    // Laravel inyecta el modelo automáticamente
    public function mount($id)
    {
        $this->ordenanza = Ordenanza332::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.ordenanzas.show');
    }
}
