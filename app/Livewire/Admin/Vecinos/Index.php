<?php

namespace App\Livewire\Admin\Vecinos;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Index extends Component
{
    public $lista = [];

    public function mount()
    {
        $this->lista = User::whereHas('vecino')
            ->with([
                'vecino',
                'vecino.barrio'
            ])
            ->orderBy('last_name')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.vecinos.index', [
            'usuarios' => $this->lista
        ]);
    }
}
