<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Services\RoleMenuService;
use Illuminate\Support\Facades\Auth;

class Izquierdo extends Component
{
    protected RoleMenuService $roleMenuService;
    public $menuItems;

    public function mount(RoleMenuService $roleMenuService)
    {
        $this->roleMenuService = $roleMenuService;

if (Auth::check()) {
    $user = Auth::user();
    $userId = $user->id;

    try {
        // Si el usuario es admin, no mostrar menú de operaciones
        if ($user->role === 'Admin') {
            $this->menuItems = []; 
        } else {
            // Para otros roles, sí cargar menú de operaciones
            $this->menuItems = $this->roleMenuService->getMenuByUserId($userId);
        }
    } catch (\Exception $e) {
        // Maneja el error según tus necesidades
        $this->menuItems = [];
    }
} else {
    // Usuario no autenticado
    $this->menuItems = [];
}

    }

    public function render()
    {
        return view('livewire.public.izquierdo');
    }
}
