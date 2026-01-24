<?php

namespace App\Services\Contracts;

interface RoleMenuServiceInterface
{
    public function getUserRole(int $userId): ?array;

    public function getMenuByRoleName(int $roleId);

    public function getMenuByUserId($userId);
}
