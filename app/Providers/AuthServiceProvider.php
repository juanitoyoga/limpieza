<?php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{Nomination, User, Resolucion};

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Contrato::class => \App\Policies\ContratoPolicy::class,
    ];

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

        Gate::define(
            'admin-access',
            fn($user) =>
            in_array($user->role_name, ['Admin', 'SuperAdmin'])
        );

        Gate::define(
            'operations-access',
            fn($user) =>
            $user->role_name !== 'user'
        );
        /*
|--------------------------------------------------------------------------
| Resoluciones
|--------------------------------------------------------------------------
*/
        // Crear resolución
        Gate::define('resoluciones.create', function (User $user) {
            return in_array($user->role_name, [

                'Dirigente',
                'User',
                'Presidente',
                'Vecino',
            ]);
        });

        // Editar resolución (solo mientras esté pendiente)
        Gate::define('resoluciones.edit', function (User $user, Resolucion $resolucion) {
            return in_array($user->role_name, [
                'Dirigente',
                'User',
                'Presidente',
                'Vecino',
            ])
                && $resolucion->auth_status === Resolucion::ESTADO_PENDIENTE;
        });

        // Verificar resolución: debe ser el Dirigente activo de ESE barrio
        Gate::define('resoluciones.verificar', function (User $user, Resolucion $resolucion) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Dirigente') {
                return false;
            }

            return \App\Models\Dirigente::where('user_id', $user->id)
                ->where('barrio_id', $resolucion->barrio_id)
                ->where('is_active', true)
                ->exists();
        });

        // Aprobar resolución: debe ser el Presidente activo de ESE barrio
        Gate::define('resoluciones.aprobar', function (User $user, Resolucion $resolucion) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Presidente') {
                return false;
            }

            return \App\Models\Presidente::where('user_id', $user->id)
                ->where('barrio_id', $resolucion->barrio_id)
                ->where('is_active', true)
                ->exists();
        });

        // Rechazar resolución: el Dirigente o Presidente de ese barrio, según la etapa
        Gate::define('resoluciones.rechazar', function (User $user, Resolucion $resolucion) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name === 'Dirigente') {
                return \App\Models\Dirigente::where('user_id', $user->id)
                    ->where('barrio_id', $resolucion->barrio_id)
                    ->where('is_active', true)
                    ->exists();
            }

            if ($user->role_name === 'Presidente') {
                return \App\Models\Presidente::where('user_id', $user->id)
                    ->where('barrio_id', $resolucion->barrio_id)
                    ->where('is_active', true)
                    ->exists();
            }

            return false;
        });

        // Ver cualquier resolución
        Gate::define('resoluciones.view', function (User $user) {
            return in_array($user->role_name, [
                'SuperAdmin',
                'Funcionario',
                'Supervisor',
                'Dirigente',
                'Presidente',
            ]);
        });
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
