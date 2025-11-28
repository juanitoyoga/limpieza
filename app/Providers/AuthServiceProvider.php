<?php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Roles: superadmin, ciudadano, dirigente, presidente, funcionario, supervisor, auditor
        // El rol de superadmin se crea al momento de implementar la aplicacion y es solo uno designado por la entidad que usa la aplicacion
        
        // Dashboard y reportes
        Gate::define('view-dashboard', fn($user) => in_array($user->role, ['superadmin', 'admin', 'manager']));
        Gate::define('view-reports', fn($user) => in_array($user->role, ['superadmin', 'admin', 'manager']));
        Gate::define('export-reports', fn($user) => in_array($user->role, ['superadmin', 'admin']));
        
        // Gestión de usuarios
        Gate::define('view-users', fn($user) => in_array($user->role, ['superadmin', 'admin']));
        Gate::define('create-users', fn($user) => in_array($user->role, ['superadmin', 'admin']));
        Gate::define('edit-users', fn($user) => in_array($user->role, ['superadmin', 'admin']));
        Gate::define('delete-users', fn($user) => $user->role === 'superadmin');
        
        // Configuración del sistema
        Gate::define('view-settings', fn($user) => $user->role === 'superadmin');
        Gate::define('edit-settings', fn($user) => $user->role === 'superadmin');
        
        // Productos/Inventario
        Gate::define('view-products', fn($user) => true); // Todos pueden ver
        Gate::define('create-products', fn($user) => in_array($user->role, ['superadmin', 'admin', 'manager']));
        Gate::define('edit-products', fn($user) => in_array($user->role, ['superadmin', 'admin', 'manager']));
        Gate::define('delete-products', fn($user) => in_array($user->role, ['superadmin', 'admin']));
        
        // Clientes
        Gate::define('view-clients', fn($user) => true);
        Gate::define('create-clients', fn($user) => in_array($user->role, ['superadmin', 'admin', 'manager']));
        Gate::define('edit-clients', fn($user) => in_array($user->role, ['superadmin', 'admin', 'manager']));
        Gate::define('delete-clients', fn($user) => in_array($user->role, ['superadmin', 'admin']));

        Gate::define('admin-access', function ($user) {
            return $user->role; // o la condición que uses
        });
    }
}
