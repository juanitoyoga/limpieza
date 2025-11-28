<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\RoleMenuServiceInterface;

class RoleMenuService implements RoleMenuServiceInterface
{
    public function getUserRoles($userId)
    {
        $userRoles = DB::table('users as u')
            ->join('user_roles as ur', 'u.id', '=', 'ur.user_id')
            ->join('roles as r', 'ur.role_id', '=', 'r.id')
            ->select(
                'u.id as user_id',
                'u.first_name as user_name',
                'r.id as role_id',
                'r.name as role_name'
            )
            ->where('u.id', $userId)
            ->get();

        return $userRoles;
    }

    public function getMenuByRoleName($roleId)
    {
        $menuByRole = DB::table('roles as r')
            ->join('menu_items as mi', 'r.id', '=', 'mi.role_id')
            ->leftJoin('menu_items as parent', 'mi.parent_id', '=', 'parent.id')
            ->select(
                'r.id as role_id',
                'r.name as role_name',
                'mi.id as menu_item_id',
                'mi.name as menu_label',
                'mi.visible as menu_option',
                'mi.parent_id as parent_menu_id',
                'mi.order as menu_order',
                'mi.route as menu_url',
                'mi.icon as menu_icon'
            )
            ->where('r.id', $roleId)
            ->orderBy('mi.parent_id', 'asc')
            ->orderBy('mi.order', 'asc')
            ->get();

        return $menuByRole;
    }

    public function getMenuByUserId($userId)
    {
        $userRoles = $this->getUserRoles($userId);
        
        foreach ($userRoles as $user) {
            $roleId = $user->role_id;
        }

        $menuByUser = $this->getMenuByRoleName($roleId);
        return $menuByUser;
    }
}
