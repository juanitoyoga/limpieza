<?php

namespace App\Livewire\Admin\Userroles;

use Livewire\Component;

use App\Models\Userrole;

use Livewire\Attributes\Layout;


#[Layout('layouts.admin')]
class Show extends Component
{
    public $userrole;

    // Laravel inyecta el modelo automáticamente
    public function mount($id)
    {
        $this->userrole = Userrole::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.userroles.show');
    }
}
