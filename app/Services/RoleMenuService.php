<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\RoleMenuServiceInterface;



class RoleMenuService implements RoleMenuServiceInterface
{



        
    public function getUserRole(int $userId): ?array
    {
        $user = User::find($userId);
    
        if (!$user || empty($user->role_name)) {
            Log::warning('Usuario sin role_name', [
                'user_id' => $userId
            ]);
            return null;
        }
    
        $role = DB::table('roles')
            ->where('name', $user->role_name)
            ->first();
    
        if (!$role) {
            Log::error('role_name no existe en tabla roles', [
                'user_id'   => $userId,
                'role_name' => $user->role_name
            ]);
            return null;
        }
    
        Log::debug('Rol resuelto correctamente', [
            'user_id'   => $user->id,
            'role_id'   => $role->id,
            'role_name' => $role->name
        ]);
    
        return [
            'user_id'   => $user->id,
            'user_name' => trim("{$user->first_name} {$user->last_name}"),
            'role_id'   => $role->id,
            'role_name' => $role->name,
        ];
    }
    
    
    
public function getMenuByRoleName(int $roleId)
{
    try {
        // Validación del parámetro de entrada
        if (empty($roleId) || !is_numeric($roleId)) {
            throw new \InvalidArgumentException('El ID del rol debe ser un número válido');
        }
        
        $roleId = (int) $roleId;
        
        if ($roleId <= 0) {
            throw new \InvalidArgumentException('El ID del rol debe ser mayor a 0');
        }

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

        // Validar si se encontraron resultados
        if ($menuByRole->isEmpty()) {
            Log::info("No se encontraron menús para el rol ID: {$roleId}");
            return collect(); // Retorna colección vacía en lugar de null
        }

        return $menuByRole;

    } catch (\InvalidArgumentException $e) {
        Log::warning('Parámetro inválido en getMenuByRoleName', [
            'roleId' => $roleId ?? 'no definido',
            'error' => $e->getMessage()
        ]);
        throw $e; // Re-lanzar para manejo superior

    } catch (\Illuminate\Database\QueryException $e) {
        // Error específico de base de datos
        Log::error('Error de base de datos en getMenuByRoleName', [
            'roleId' => $roleId,
            'error' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings()
        ]);

        // Verificar tipo específico de error
        $errorCode = $e->getCode();
        
        if ($e->getCode() == 2002 || str_contains($e->getMessage(), 'Connection refused')) {
            throw new \App\Exceptions\DatabaseConnectionException(
                'No se puede conectar a la base de datos',
                $errorCode,
                $e
            );
        }

        if ($e->getCode() == 1045) {
            throw new \App\Exceptions\DatabaseAuthException(
                'Error de autenticación en la base de datos',
                $errorCode,
                $e
            );
        }

        throw new \App\Exceptions\MenuQueryException(
            'Error al consultar el menú del rol',
            $errorCode,
            $e
        );

    } catch (\PDOException $e) {
        // Error PDO (drivers de base de datos)
        Log::critical('Error PDO en getMenuByRoleName', [
            'roleId' => $roleId,
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ]);

        throw new \App\Exceptions\DatabaseException(
            'Error del sistema de base de datos',
            $e->getCode(),
            $e
        );

    } catch (\Exception $e) {
        // Captura cualquier otra excepción no prevista
        Log::error('Error inesperado en getMenuByRoleName', [
            'roleId' => $roleId,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        throw new \App\Exceptions\MenuServiceException(
            'Error al obtener el menú del rol',
            0,
            $e
        );
    }
}

public function getMenuByUserId($userId)
{
    try {
        $userRole = $this->getUserRole($userId); 
        
        if (empty($userRole)) {
            Log::info("Usuario {$userId} sin rol válido.");
            return collect();
        }

        return $this->getMenuByRoleName($userRole['role_id']);

    } catch (\App\Exceptions\MenuServiceException $e) {
        Log::error("Error de negocio: " . $e->getMessage());
        return collect();

    } catch (\Exception $e) {
        Log::critical("Fallo crítico: " . $e->getMessage());
        throw $e; 
    }
}

}
