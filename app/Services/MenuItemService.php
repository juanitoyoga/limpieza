<?php

namespace App\Services;

use App\Repositories\MenuItemRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class MenuItemService
{
    public function __construct(
        protected MenuItemRepositoryInterface $repository
    ) {}

    public function listar(): mixed
    {
        return $this->repository->all();
    }

    public function obtener(int $id): mixed
    {
        return $this->repository->find($id);
    }

    public function crear(array $data): mixed
    {
        return $this->repository->create($data);
    }

    public function actualizar(int $id, array $data): mixed
    {
        return $this->repository->update($id, $data);
    }

    public function eliminar(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function menuPorRol(int $roleId): mixed
    {
        return $this->repository->getMenuByRole($roleId);
    }

    public function hijos(int $parentId): mixed
    {
        return $this->repository->getChildren($parentId);
    }
    /**
     * Devuelve el menú para el usuario.
     * Si $userId es null, toma el usuario autenticado (Auth::id()).
     */
    public function getMenu(?int $userId = null): mixed
    {
        $userId = $userId ?? Auth::id();

        if (is_null($userId)) {
            // Usuario no autenticado: devolver solo items públicos
            return $this->repository->getPublicMenu();
        }

        return $this->repository->getMenuForUser($userId);
    }
    public function getMenuForUser($user)
    {
        $roles = $user->obtiene_rol();


        return $this->repository->getMenuByRoles($roles);
    }
}
