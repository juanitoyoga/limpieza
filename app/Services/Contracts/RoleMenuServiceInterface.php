<?php

namespace App\Services\Contracts;

interface RoleMenuServiceInterface
{
    public function getUserRoles($userId);

    public function getMenuByRoleName($roleName);

    public function getMenuByUserId($userId);
}
