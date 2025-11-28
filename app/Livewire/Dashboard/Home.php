<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.dashboard.home');
    }
}
