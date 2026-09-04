<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Contrato;
use App\Models\Barrio;
use App\Models\Funcionario;
use App\Models\Supervisor;
use App\Models\Auditor;

class ContratoSeeder extends Seeder
{
    /**
     * Distribución de estados para 60 contratos:
     *   pendiente   → 12  (20 %)
     *   verificado  → 12  (20 %)
     *   aprobado    → 12  (20 %)
     *   finalizado  → 12  (20 %)
     *   rechazado   →  6  (10 %)
     *   anulado     →  6  (10 %)
     */
    private const DISTRIBUCION = [
        Contrato::ESTADO_PENDIENTE   => 12,
        Contrato::ESTADO_VERIFICADO  => 12,
        Contrato::ESTADO_APROBADO    => 12,
        Contrato::ESTADO_FINALIZADO  => 12,
        Contrato::ESTADO_RECHAZADO   =>  6,
        Contrato::ESTADO_ANULADO     =>  6,
    ];

    public function run(): void
    {
        // ── Cargar actores ────────────────────────────────────────────────────
        $barrios      = Barrio::activos()->pluck('id')->all();
        $funcionarios = Funcionario::where('is_active', true)->pluck('id')->all();
        $supervisores = Supervisor::where('is_active', true)->pluck('id')->all();
        $auditores    = Auditor::where('is_active', true)->pluck('id')->all();

        abort_if(empty($barrios),      500, 'ContratoSeeder: no hay barrios activos.');
        abort_if(count($funcionarios) < 1, 500, 'ContratoSeeder: se necesita al menos 1 funcionario activo.');
        abort_if(count($supervisores) < 1, 500, 'ContratoSeeder: se necesita al menos 1 supervisor activo.');
        abort_if(count($auditores)    < 1, 500, 'ContratoSeeder: se necesita al menos 1 auditor activo.');

        $porcentajes = [
            ['barrio' => 60, 'dmq' => 30, 'ltr' => 10],
            ['barrio' => 50, 'dmq' => 35, 'ltr' => 15],
            ['barrio' => 55, 'dmq' => 25, 'ltr' => 20],
            ['barrio' => 70, 'dmq' => 20, 'ltr' => 10],
        ];

        $numero = 1000; // base para numero_contrato

        DB::transaction(function () use (
            $barrios,
            $funcionarios,
            $supervisores,
            $auditores,
            $porcentajes,
            &$numero
        ) {
            foreach (self::DISTRIBUCION as $estado => $cantidad) {
                for ($i = 0; $i < $cantidad; $i++) {
                    $numero++;
                    $this->crearContrato(
                        estado: $estado,
                        numero: $numero,
                        barrios: $barrios,
                        funcionarios: $funcionarios,
                        supervisores: $supervisores,
                        auditores: $auditores,
                        porcentajes: $porcentajes,
                    );
                }
            }
        });

        $total = array_sum(self::DISTRIBUCION);
        $this->command->info("✅  ContratoSeeder: {$total} contratos creados.");
        foreach (self::DISTRIBUCION as $estado => $cantidad) {
            $this->command->line("    {$estado}: {$cantidad}");
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function crearContrato(
        string $estado,
        int    $numero,
        array  $barrios,
        array  $funcionarios,
        array  $supervisores,
        array  $auditores,
        array  $porcentajes,
    ): void {
        $pct         = $porcentajes[array_rand($porcentajes)];
        $fechaInicio = now()->subMonths(rand(1, 18));
        $fechaFin    = (clone $fechaInicio)->addMonths(rand(6, 24));
        $montoTotal  = rand(5_000, 200_000) + (rand(0, 99) / 100);

        // Actores – garantizamos IDs distintos entre sí
        $funcionarioId = $funcionarios[array_rand($funcionarios)];
        $supervisorId  = $supervisores[array_rand($supervisores)];
        $auditorId     = $auditores[array_rand($auditores)];

        $contrato = Contrato::create([
            'barrio_id'           => $barrios[array_rand($barrios)],
            'numero_contrato'     => 'LTR-' . str_pad($numero, 6, '0', STR_PAD_LEFT),
            'fecha_inicio'        => $fechaInicio->toDateString(),
            'fecha_fin'           => $fechaFin->toDateString(),
            'monto_total'         => $montoTotal,
            'porcentaje_barrio'   => $pct['barrio'],
            'porcentaje_dmq'      => $pct['dmq'],
            'porcentaje_ltr'      => $pct['ltr'],
            'estado'              => Contrato::ESTADO_PENDIENTE, // arrancamos limpio
            // Ingreso siempre presente
            'id_ingreso'          => $funcionarioId,
            'rol_ingreso'         => 'funcionario',
            'fecha_ingreso'       => $fechaInicio->copy()->addDays(rand(0, 5)),
        ]);

        // Avanzar el flujo según el estado deseado
        match ($estado) {
            Contrato::ESTADO_PENDIENTE  => null,            // ya está
            Contrato::ESTADO_VERIFICADO => $this->verificar($contrato, $supervisorId, $fechaInicio),
            Contrato::ESTADO_APROBADO   => $this->aprobar($contrato, $supervisorId, $auditorId, $fechaInicio),
            Contrato::ESTADO_FINALIZADO => $this->finalizar($contrato, $supervisorId, $auditorId, $fechaInicio),
            Contrato::ESTADO_RECHAZADO  => $this->rechazar($contrato, $supervisorId, $fechaInicio),
            Contrato::ESTADO_ANULADO    => $this->anular($contrato),
            default                     => null,
        };
    }

    // ── Pasos del flujo ───────────────────────────────────────────────────────

    private function verificar(Contrato $c, int $supervisorId, \Carbon\Carbon $base): void
    {
        $c->update([
            'id_verificacion'    => $supervisorId,
            'rol_verificacion'   => 'supervisor',
            'fecha_verificacion' => $base->copy()->addDays(rand(6, 15)),
            'estado'             => Contrato::ESTADO_VERIFICADO,
        ]);
    }

    private function aprobar(Contrato $c, int $supervisorId, int $auditorId, \Carbon\Carbon $base): void
    {
        $this->verificar($c, $supervisorId, $base);

        $c->update([
            'id_aprobacion'    => $auditorId,
            'rol_aprobacion'   => 'auditor',
            'fecha_aprobacion' => $base->copy()->addDays(rand(16, 30)),
            'estado'           => Contrato::ESTADO_APROBADO,
        ]);
    }

    private function finalizar(Contrato $c, int $supervisorId, int $auditorId, \Carbon\Carbon $base): void
    {
        $this->aprobar($c, $supervisorId, $auditorId, $base);

        $c->update([
            'fecha_pago'         => $base->copy()->addDays(rand(31, 45)),
            'fecha_distribucion' => $base->copy()->addDays(rand(46, 60)),
            'estado'             => Contrato::ESTADO_FINALIZADO,
            // Simulamos datos blockchain para contratos finalizados
            'document_hash'      => hash('sha256', $c->numero_contrato . $c->monto_total),
            'blockchain_tx'      => '0x' . bin2hex(random_bytes(32)),
            'blockchain_network' => fake()->randomElement(['polygon', 'ethereum', 'bsc']),
            'wallet_address'     => '0x' . bin2hex(random_bytes(20)),
            'tx_hash'            => '0x' . bin2hex(random_bytes(32)),
            'blockchain_at'      => $base->copy()->addDays(rand(46, 60)),
        ]);
    }

    private function rechazar(Contrato $c, int $supervisorId, \Carbon\Carbon $base): void
    {
        // El supervisor verifica y rechaza en el mismo paso
        $c->update([
            'id_verificacion'    => $supervisorId,
            'rol_verificacion'   => 'supervisor',
            'fecha_verificacion' => $base->copy()->addDays(rand(6, 15)),
            'estado'             => Contrato::ESTADO_RECHAZADO,
        ]);
    }

    private function anular(Contrato $c): void
    {
        $c->update(['estado' => Contrato::ESTADO_ANULADO]);
    }
}
