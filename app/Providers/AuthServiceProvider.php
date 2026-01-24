<?php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{Nomination, User};

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

        Gate::define('admin-access', fn($user) =>
            in_array($user->role_name, ['Admin', 'SuperAdmin'])
        );

        Gate::define('operations-access', fn($user) =>
            $user->role_name !== 'user'
            );

        /*
        |--------------------------------------------------------------------------
        | Nominations
        |--------------------------------------------------------------------------
        */
    
        // Crear nominación
        Gate::define('nominations.create', function (User $user) {
            return in_array($user->role_name, [
                'SuperAdmin',
                'Funcionario',
                'Supervisor',
                'Auditor',
            ]);
        });
    
        // Editar nominación (solo mientras esté en PROPUESTA)
        Gate::define('nominations.edit', function (User $user, Nomination $nomination) {
            return in_array($user->role_name, ['SuperAdmin', 'Supervisor', 'Funcionario', 'Auditor'])
                && $nomination->estado === Nomination::ESTADO_PROPUESTA;
        });
    
        // Verificar nominación
        Gate::define('nominations.verificar', function (User $user) {
            return in_array($user->role_name, [
                'Supervisor',
                'Auditor',
                'Funcionario',
                'SuperAdmin',
            ]);
        });
    
        // Aprobar nominación
        Gate::define('nominations.aprobar', function (User $user) {
            return in_array($user->role_name, [
                'Supervisor',
                'Auditor',
                'Funcionario',
                'SuperAdmin',
            ]);
        });
    
        // Rechazar nominación
        Gate::define('nominations.rechazar', function (User $user) {
            return in_array($user->role_name, [
                'Supervisor',
                'Auditor',
                'Funcionario',
                'SuperAdmin',
            ]);
        });            
        // Ver cualquier nominación
        Gate::define('nominations.view', function (User $user) {
            return in_array($user->role_name, [
                'SuperAdmin',
                'Funcionario',
                'Supervisor',
                'Auditor',
            ]);
        });
            

    }
}
