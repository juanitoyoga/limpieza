<?php

namespace App\Services;

use App\Repositories\MenuRepository;

class MenuService
{
    
    protected $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function getMenu()
    {
        return $this->menuRepository->getMenuForLoggedUser();
    }
}
