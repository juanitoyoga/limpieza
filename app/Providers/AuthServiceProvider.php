<?php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;
use App\Models\{Nomination, User, Resolucion, Oferta, ContratoServicio};

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Contrato::class => \App\Policies\ContratoPolicy::class,
    ];

    public function boot(): void
    {

        Gate::define('cms.proponer', fn($user) => in_array($user->role_name, ['Funcionario', 'Dirigente', 'SuperAdmin'], true));
        Gate::define('cms.aprobar', fn($user) => in_array($user->role_name, ['Supervisor', 'SuperAdmin'], true));
        Gate::define('cms.gestionsecciones', fn($user) => in_array($user->role_name, ['Supervisor', 'SuperAdmin'], true));

        Gate::define('cms.historial', fn($user) => in_array($user->role_name, ['Funcionario', 'Dirigente', 'Supervisor', 'SuperAdmin'], true));

        Gate::define('cms.ver', fn($user) => in_array($user->role_name, ['Funcionario', 'Dirigente', 'Supervisor', 'SuperAdmin'], true));


        /*
        |--------------------------------------------------------------------------
        | Órdenes de Pago
        |--------------------------------------------------------------------------
        */

        Gate::define('ordenes-pago.ver', function (User $user, ContratoServicio $contrato) {
            if (in_array($user->role_name, ['SuperAdmin', 'Presidente', 'Auditor', 'Funcionario'], true)) {
                return true;
            }

            if ($user->role_name === 'Dirigente') {
                return $user->barrioComoResponsable() === $contrato->resolucion?->barrio_id;
            }

            return false;
        });

        Gate::define('ordenes-pago.registrar', function (User $user, ContratoServicio $contrato) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Dirigente') {
                return false;
            }

            return \App\Models\Dirigente::where('user_id', $user->id)
                ->where('barrio_id', $contrato->resolucion?->barrio_id)
                ->where('is_active', true)
                ->exists();
        });

        Gate::define('ordenes-pago.autorizar', function (User $user, ContratoServicio $contrato) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Presidente') {
                return false;
            }

            return \App\Models\Presidente::where('user_id', $user->id)
                ->where('barrio_id', $contrato->resolucion?->barrio_id)
                ->where('is_active', true)
                ->exists();
        });

        Gate::define('ordenes-pago.pagar', function (User $user, ContratoServicio $contrato) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name === 'Presidente') {
                return \App\Models\Presidente::where('user_id', $user->id)
                    ->where('barrio_id', $contrato->resolucion?->barrio_id)
                    ->where('is_active', true)
                    ->exists();
            }

            if ($user->role_name === 'Dirigente') {
                return \App\Models\Dirigente::where('user_id', $user->id)
                    ->where('barrio_id', $contrato->resolucion?->barrio_id)
                    ->where('is_active', true)
                    ->exists();
            }

            return false;
        });

        Gate::define('ordenes-pago.anular', function (User $user, ContratoServicio $contrato) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name === 'Presidente') {
                return \App\Models\Presidente::where('user_id', $user->id)
                    ->where('barrio_id', $contrato->resolucion?->barrio_id)
                    ->where('is_active', true)
                    ->exists();
            }

            if ($user->role_name === 'Dirigente') {
                return \App\Models\Dirigente::where('user_id', $user->id)
                    ->where('barrio_id', $contrato->resolucion?->barrio_id)
                    ->where('is_active', true)
                    ->exists();
            }

            return false;
        });

        // Agregar en app/Providers/AuthServiceProvider.php, dentro de boot():
        /*
        |--------------------------------------------------------------------------
        | HitosContratoServicio / Verificación y Aprobación de Servicios
        |--------------------------------------------------------------------------
        */

        // Permite acceder al panel/listado general de gestión de hitos
        Gate::define('contratos-servicios.gestion-hitos', function (User $user) {
            return in_array($user->role_name, ['Dirigente', 'Presidente', 'SuperAdmin', 'Admin'], true);
        });

        Gate::define('contratos-servicios.gestion-ordenes-pago', function (User $user) {
            return in_array($user->role_name, ['Dirigente', 'Presidente', 'SuperAdmin', 'Admin'], true);
        });


        // Iniciar Verificación cuando NO existe hito (Paso 1: Dirigente valida ANTES y DESPUÉS)
        Gate::define('iniciarverificacion', function (User $user, ?\App\Models\ContratoServicioDetalle $detalle = null) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Dirigente') {
                return false;
            }

            // Chequeo general para entrar al panel
            if ($detalle === null) {
                return true;
            }

            $barrioResponsable = $user->barrioComoResponsable();

            if ($barrioResponsable === null) {
                return false;
            }

            // Requiere que el detalle pertenezca al barrio del dirigente y tenga ejecucion completa
            return $barrioResponsable === $detalle->contratoServicio->resolucion?->barrio_id
                && $detalle->ejecucionCompleta()
                && !$detalle->hito()->exists();
        });

        // Verificar Hito existente
        Gate::define('verificar-hito', function (User $user, \App\Models\HitoContratoServicio $hito) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Dirigente') {
                return false;
            }

            $barrioResponsable = $user->barrioComoResponsable();

            if ($barrioResponsable === null) {
                return false;
            }

            return $barrioResponsable === $hito->detalle->contratoServicio->resolucion?->barrio_id;
        });

        // Aprobar Hito existente (Presidente del barrio)
        Gate::define('aprobar-hito', function (User $user, \App\Models\HitoContratoServicio $hito) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Presidente') {
                return false;
            }

            $barrioResponsable = $user->barrioComoResponsable();

            if ($barrioResponsable === null) {
                return false;
            }

            return $barrioResponsable === $hito->detalle->contratoServicio->resolucion?->barrio_id;
        });

        Gate::define('asignar-contratistas', function (User $user, ContratoServicio $contrato) {
            if (! in_array($user->role_name, ['Dirigente', 'Presidente'], true)) {
                return false;
            }

            $barrioResponsable = $user->barrioComoResponsable();

            if ($barrioResponsable === null) {
                return false; // Dirigente/Presidente sin barrio activo asignado
            }

            return $barrioResponsable === $contrato->resolucion?->barrio_id;
        });


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

        //Logs del sistema
        // en AuthServiceProvider o donde tengas tus Gates
        Gate::define('logs-sistema.ver', fn($user) => $user->role_name === 'Admin');
        Gate::define('logs-sistema.eliminar', fn($user) => $user->role_name === 'Admin');

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

        Gate::define('gestionar-sesiones', function (User $user) {
            return $user->role_name === 'Admin'; // ajustar al nombre real del rol si es distinto
        });

        /*
        |--------------------------------------------------------------------------
        | ContratoServicio (contrato con proveedor — distinto del Contrato de
        | distribución de multas)
        |--------------------------------------------------------------------------
        */
        // Agregar en app/Providers/AuthServiceProvider.php, dentro de boot():

        Gate::define('asignar-contratistas', function (User $user, ContratoServicio $contrato) {
            if (! in_array($user->role_name, ['Dirigente', 'Presidente'], true)) {
                return false;
            }

            $barrioResponsable = $user->barrioComoResponsable();

            if ($barrioResponsable === null) {
                return false; // Dirigente/Presidente sin barrio activo asignado
            }

            return $barrioResponsable === $contrato->resolucion?->barrio_id;
        });


        Gate::define('contratos-servicios.ver', function (User $user) {
            return in_array($user->role_name, ['Dirigente', 'Presidente', 'Admin']);
        });

        Gate::define('contratos-servicios.buscar', function (User $user) {
            return in_array($user->role_name, ['Dirigente', 'Presidente', 'Admin']);
        });

        Gate::define('contratos-servicios.crear', function (User $user) {
            return in_array($user->role_name, ['Dirigente', 'Presidente', 'Admin']);
        });


        Gate::define('contratos-servicios.verificar', function (User $user, ContratoServicio $contrato) {
            return $user->role_name === 'Dirigente';
        });

        Gate::define('contratos-servicios.aprobar', function (User $user, ContratoServicio $contrato) {
            return $user->role_name === 'Presidente';
        });

        Gate::define('contratos-servicios.rechazar', function (User $user, ContratoServicio $contrato) {
            return in_array($user->role_name, ['Dirigente', 'Presidente']);
        });

        Gate::define('contratos-servicios.rescindir', function (User $user, ContratoServicio $contrato) {
            return $user->role_name === 'Presidente';
        });

        Gate::define('contratos-servicios.liquidar', function (User $user, ContratoServicio $contrato) {
            return $user->role_name === 'Presidente';
        });
        /*
        |--------------------------------------------------------------------------
        | Ofertas
        |--------------------------------------------------------------------------
        */

        // Crear oferta (registro de la oferta de un proveedor para una resolución)
        Gate::define('ofertas.create', function (User $user) {
            return in_array($user->role_name, [
                'Dirigente',
                'Presidente',
            ]);
        });

        // Editar servicios de la oferta (solo mientras esté Pendiente; el propio
        // componente Livewire valida el estado además de este Gate)
        Gate::define('ofertas.editarServicios', function (User $user, Oferta $oferta) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name === 'Dirigente') {
                return $user->barrioComoResponsable() === $oferta->resolucion->barrio_id;
            }

            return false;
        });

        // Verificar oferta (documento físico vs. hash): el Dirigente activo del barrio
        Gate::define('ofertas.verificar', function (User $user, Oferta $oferta) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Dirigente') {
                return false;
            }

            return $user->barrioComoResponsable() === $oferta->resolucion->barrio_id;
        });

        // Aprobar oferta (define la ganadora entre proveedores): el Presidente activo del barrio
        Gate::define('ofertas.aprobar', function (User $user, Oferta $oferta) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if ($user->role_name !== 'Presidente') {
                return false;
            }

            return $user->barrioComoResponsable() === $oferta->resolucion->barrio_id;
        });

        // Rechazar oferta: el Dirigente o Presidente de ese barrio, según la etapa
        Gate::define('ofertas.rechazar', function (User $user, Oferta $oferta) {
            if ($user->role_name === 'SuperAdmin') {
                return true;
            }

            if (in_array($user->role_name, ['Dirigente', 'Presidente'])) {
                return $user->barrioComoResponsable() === $oferta->resolucion->barrio_id;
            }

            return false;
        });

        // Ver ofertas: personal municipal con visión global (para auditoría DMQ)
        // + Dirigente/Presidente (el filtro por barrio específico se aplica en la
        // query de Lista.php, este Gate solo controla acceso general al módulo)
        Gate::define('ofertas.ver', function (User $user) {
            return in_array($user->role_name, [
                'SuperAdmin',
                'Funcionario',
                'Supervisor',
                'Auditor',
                'Dirigente',
                'Presidente',
            ]);
        });

        // Ver detalle de UNA oferta puntual (solo lectura), acotado por barrio
        // para Dirigente/Presidente; usar en cualquier componente Show.php futuro
        Gate::define('ofertas.verDetalle', function (User $user, Oferta $oferta) {
            if (in_array($user->role_name, ['SuperAdmin', 'Funcionario', 'Supervisor', 'Auditor'])) {
                return true;
            }

            if (in_array($user->role_name, ['Dirigente', 'Presidente'])) {
                return $user->barrioComoResponsable() === $oferta->resolucion->barrio_id;
            }

            return false;
        });
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


        Gate::define('resoluciones.aprobar', function (User $user, Resolucion $resolucion) {

            // 1. SuperAdmin siempre pasa
            if ($user->role_name === 'SuperAdmin') {
                return Response::allow();
            }

            // 2. Si no es Presidente, denegar con mensaje
            if ($user->role_name !== 'Presidente') {
                return Response::deny('Solo un Presidente o SuperAdmin puede aprobar resoluciones.');
            }

            // 3. Verificar que sea presidente ACTIVO de ese barrio
            $esPresidenteDelBarrio = \App\Models\Presidente::where('user_id', $user->id)
                ->where('barrio_id', $resolucion->barrio_id)
                ->where('is_active', true)
                ->exists();

            if (!$esPresidenteDelBarrio) {
                return Response::deny('No eres Presidente activo de este barrio, no puedes aprobar esta resolución.');
            }

            return Response::allow();
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
