<?php

namespace App\Services;

use App\Models\AsignacionContratoServicio;
use App\Models\Contacto;
use App\Models\Contratista;
use App\Models\ContratoServicio;
use App\Models\User;
use App\Jobs\EnviarCredencialesContratistaJob;
use App\Jobs\EnviarRolActualizadoContratistaJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Orquesta el ciclo de vida de un Contratista respecto a los contratos de
 * servicio de su Proveedor:
 *
 *  - Contratista.is_active   → ¿esta persona está habilitada en general?
 *                               (espeja User.is_active)
 *  - AsignacionContratoServicio.is_active → ¿tiene acceso a ESTE contrato?
 *
 * Un Contratista puede tener varias asignaciones activas (a distintos
 * contratos del mismo proveedor) simultáneamente. Solo se desactiva al
 * usuario cuando ya no le queda NINGUNA asignación activa.
 */
class ContratistaAsignacionService
{
    /**
     * Asigna un Contacto del proveedor a un ContratoServicio aprobado.
     * Si el Contacto no tiene Contratista todavía, lo crea (junto con su
     * User). Si ya lo tiene pero está inactivo, lo reactiva.
     *
     * @throws \DomainException si el contrato no está Aprobada, si el
     *         contacto no tiene email, o si el usuario ya tiene otro rol.
     */
    public function asignar(Contacto $contacto, ContratoServicio $contrato, User $actor): AsignacionContratoServicio
    {
        if ($contrato->auth_status !== ContratoServicio::ESTADO_APROBADA) {
            throw new \DomainException(
                'Solo se puede asignar personal a un contrato en estado Aprobada.'
            );
        }

        if ($contacto->proveedor_id !== $contrato->proveedor_id) {
            throw new \DomainException(
                'El contacto no pertenece al proveedor de este contrato.'
            );
        }

        return DB::transaction(function () use ($contacto, $contrato, $actor) {
            $contratista = $this->obtenerOCrearContratista($contacto);

            $asignacion = AsignacionContratoServicio::where('contratista_id', $contratista->id)
                ->where('contrato_servicio_id', $contrato->id)
                ->first();

            if ($asignacion) {
                $asignacion->update(['is_active' => true, 'asignado_por' => $actor->id]);
            } else {
                $asignacion = AsignacionContratoServicio::create([
                    'contratista_id'       => $contratista->id,
                    'contrato_servicio_id' => $contrato->id,
                    'asignado_por'         => $actor->id,
                    'is_active'            => true,
                ]);
            }

            $this->activarContratista($contratista);

            return $asignacion;
        });
    }

    /**
     * Revoca el acceso de un contratista a un contrato puntual. Si esa era
     * su última asignación activa, desactiva también al Contratista y su
     * User (ya no puede loguearse en la app hasta que se le reasigne).
     */
    public function revocarAsignacion(AsignacionContratoServicio $asignacion): void
    {
        DB::transaction(function () use ($asignacion) {
            $asignacion->update(['is_active' => false]);

            $contratista = $asignacion->contratista;

            if (! $contratista->asignaciones()->activas()->exists()) {
                $this->desactivarContratista($contratista);
            }
        });
    }

    /**
     * Se llama al Rescindir o Liquidar un ContratoServicio: revoca TODAS
     * las asignaciones activas de ese contrato específico (no de todos los
     * contratos del proveedor), y desactiva a cada contratista que se haya
     * quedado sin ninguna asignación activa en otro contrato.
     */
    public function revocarTodasDelContrato(ContratoServicio $contrato): void
    {
        DB::transaction(function () use ($contrato) {
            $asignaciones = AsignacionContratoServicio::activas()
                ->where('contrato_servicio_id', $contrato->id)
                ->with('contratista')
                ->get();

            foreach ($asignaciones as $asignacion) {
                $asignacion->update(['is_active' => false]);

                $contratista = $asignacion->contratista;

                if (! $contratista->asignaciones()->activas()->exists()) {
                    $this->desactivarContratista($contratista);
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers privados
    |--------------------------------------------------------------------------
    */

    private function obtenerOCrearContratista(Contacto $contacto): Contratista
    {
        $contratista = $contacto->contratista;

        if ($contratista) {
            return $contratista;
        }

        $userExistente = $this->localizarUsuarioExistente($contacto);

        if ($userExistente) {
            // Ya existe una cuenta (p. ej. la persona se registró antes en
            // la app con rol genérico 'User') — se reutiliza en vez de
            // crear una duplicada. Contratista::booted() valida que el
            // rol actual sea elegible (null o 'User') y lo cambia a
            // 'Contratista'; si tuviera otro rol, lanza excepción ahí.
            $contratista = Contratista::create([
                'contacto_id' => $contacto->id,
                'user_id'     => $userExistente->id,
                'is_active'   => true,
            ]);

            DB::afterCommit(function () use ($userExistente) {
                EnviarRolActualizadoContratistaJob::dispatch($userExistente->id)
                    ->onQueue('notificaciones');
            });

            return $contratista;
        }

        if (blank($contacto->email)) {
            throw new \DomainException(
                "El contacto {$contacto->nombre_completo} no tiene email registrado; complétalo antes de asignarlo como contratista."
            );
        }

        $passwordTemporal = Str::password(12, symbols: false); // fácil de transcribir desde el correo

        $user = User::create([
            'tipo_id'    => $contacto->tipo_id,
            'nro_id'     => $contacto->nro_id,
            'first_name' => $contacto->first_name,
            'last_name'  => $contacto->last_name,
            'email'      => $contacto->email,
            'phone'      => $contacto->phone,
            'password'   => $passwordTemporal, // el mutator de User la hashea al guardar
            'is_active'  => true,
        ]);

        // Contratista::booted() valida y asigna role_name = 'Contratista'
        // en el propio User al crearse.
        $contratista = Contratista::create([
            'contacto_id' => $contacto->id,
            'user_id'     => $user->id,
            'is_active'   => true,
        ]);

        DB::afterCommit(function () use ($user, $passwordTemporal) {
            EnviarCredencialesContratistaJob::dispatch($user->id, $passwordTemporal)
                ->onQueue('notificaciones');
        });

        return $contratista;
    }

    /**
     * Busca un User ya existente que corresponda a este Contacto, por
     * nro_id (cédula/RUC — más confiable que el email para identificar a
     * la misma persona). Solo se reutiliza si su role_name es elegible
     * (null o 'User'); si tiene otro rol lo dejamos pasar como null aquí
     * y que Contratista::booted() lance el error correspondiente al
     * intentar crearlo — así el mensaje de conflicto de rol queda en un
     * solo lugar del código.
     *
     * ⚠️ Asume que Contacto.nro_id y User.nro_id usan el mismo formato
     * (sin guiones/espacios). Ajusta si difiere.
     */
    private function localizarUsuarioExistente(Contacto $contacto): ?User
    {
        if (blank($contacto->nro_id)) {
            return null;
        }

        return User::where('nro_id', $contacto->nro_id)
            ->whereIn('role_name', [null, 'User'])
            ->first();
    }

    private function activarContratista(Contratista $contratista): void
    {
        if (! $contratista->is_active) {
            $contratista->update(['is_active' => true]);
        }

        $user = $contratista->user;

        if ($user && $user->role_name !== 'Contratista') {
            throw new \DomainException(
                "El usuario de este contratista ya tiene el rol '{$user->role_name}'; resuélvelo antes de reactivarlo."
            );
        }

        if ($user && ! $user->is_active) {
            $user->update(['is_active' => true]);
        }
    }

    private function desactivarContratista(Contratista $contratista): void
    {
        $contratista->update(['is_active' => false]);
        $contratista->user?->update(['is_active' => false]);
    }
}
