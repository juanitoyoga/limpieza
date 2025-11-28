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
            $userId = Auth::id();
            try {
                $this->menuItems = $this->roleMenuService->getMenuByUserId($userId);
            } catch (\Exception $e) {
                // Maneja el error según tus necesidades
                // Puedes lanzar una excepción personalizada o asignar un valor por defecto a $this->menuItems
                $this->menuItems = [];
            }
        } else {
            // Maneja el caso en que el usuario no está autenticado
            // Puedes redirigir al usuario a la página de inicio de sesión o asignar un valor por defecto a $this->menuItems
            $this->menuItems = [];
        }
    }

    public function render()
    {
        return view('livewire.public.izquierdo');
    }
}