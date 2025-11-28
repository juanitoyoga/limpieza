<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\RoleMenuService;
use Illuminate\Support\Facades\Auth;

class AsideMenu extends Component
{
    protected $roleMenuService;
    public $menuItems;
    public $selectedEntity = null;

    // ⭐ Inyección de dependencias PERMITIDA en mount()
    public function mount(RoleMenuService $roleMenuService)
    {
        $this->roleMenuService = $roleMenuService;

        // Obtén el ID del usuario actual
        $userId = Auth::id();

        // Obtén las entidades (menús) para el usuario
        $this->menuItems = $this->roleMenuService->getMenuByUserId($userId);
    }

    public function selectEntity($entity)
    {
        $this->selectedEntity = $entity;
    }

    public function render()
    {
        return view('livewire.admin.aside-menu', [
            'menuItems' => $this->menuItems,
            'selectedEntity' => $this->selectedEntity,
        ]);
    }
}
