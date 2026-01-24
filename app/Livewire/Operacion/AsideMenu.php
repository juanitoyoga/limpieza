<?php

namespace App\Livewire\Operacion;

use Livewire\Component;
use App\Services\RoleMenuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AsideMenu extends Component
{
    protected RoleMenuService $roleMenuService;
    public $menuItems;
    public $selectedEntity = null;    

    public function mount(RoleMenuService $roleMenuService)
    {
        Log::debug('[AsideMenu] mount() iniciado');

        $this->roleMenuService = $roleMenuService;

        if (Auth::check()) {

            $user = Auth::user();
            $userId = $user->id;

            Log::debug('[AsideMenu] Usuario autenticado', [
                'user_id'   => $userId,
                'role_name' => $user->role_name,
                'email'     => $user->email ?? null,
            ]);

            try {
                // Si el usuario es admin, no mostrar menú de operaciones
                if ($user->role_name === 'Admin') {

                    Log::debug('[AsideMenu] Usuario Admin → menú vacío');

                    $this->menuItems = [];

                } else {

                    Log::debug('[AsideMenu] Cargando menú por usuario', [
                        'user_id' => $userId
                    ]);

                    $this->menuItems = $this->roleMenuService
                        ->getMenuByUserId($userId);

                    Log::debug('[AsideMenu] Menú cargado correctamente', [
                        'items_count' => is_countable($this->menuItems)
                            ? count($this->menuItems)
                            : null,
                        'menu_items'  => $this->menuItems,
                    ]);
                }

            } catch (\Throwable $e) {

                Log::error('[AsideMenu] Error al cargar menú', [
                    'user_id' => $userId,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString(),
                ]);

                $this->menuItems = [];
            }

        } else {

            Log::warning('[AsideMenu] Usuario NO autenticado');

            $this->menuItems = [];
        }
    }

    public function selectEntity($entity)
    {
        Log::debug('[AsideMenu] selectEntity()', [
            'entity' => $entity,
        ]);

        $this->selectedEntity = $entity;
    }

    public function render()
    {
        Log::debug('[AsideMenu] render()', [
            'menu_items_count' => is_countable($this->menuItems)
                ? count($this->menuItems)
                : null,
            'selected_entity' => $this->selectedEntity,
        ]);

        return view('livewire.operacion.aside-menu', [
            'menuItems'      => $this->menuItems,
            'selectedEntity' => $this->selectedEntity,
        ]);
    }
}
