<?php

namespace App\Services;

use App\Models\Multa;
use App\Models\User;
use App\Models\IngresoContableMulta;
use App\Models\AuditEvent;
use App\Jobs\RegistrarEventoBlockchain;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/**
 * Class DistribucionContableService
 *
 * Servicio encargado de gestionar el desglose y la asignación contable
 * de los fondos recaudados por concepto de multas hacia sus diferentes beneficiarios.
 *
 * @package App\Services
 */
class DistribucionContableService
{
    /**
     * Genera los registros de ingresos contables para cada beneficiario (barrio, municipio, plataforma),
     * crea los eventos de auditoría correspondientes y programa el envío a la blockchain.
     *
     * @param Multa $multa Instancia de la multa procesada.
     * @return void
     */
    public function generarIngresos(Multa $multa): void
    {
        // Define la estructura de repartición contable según los valores y porcentajes de la multa
        $distribuciones = [
            [
                'tipo' => 'barrio',
                'porcentaje' => $multa->porcentaje_barrio,
                'monto' => $multa->valor_barrio,
                'barrio_id' => $multa->denuncia->barrio_id ?? null
            ],
            [
                'tipo' => 'municipio',
                'porcentaje' => $multa->porcentaje_municipio,
                'monto' => $multa->valor_municipio,
                'barrio_id' => null
            ],
            [
                'tipo' => 'plataforma',
                'porcentaje' => $multa->porcentaje_plataforma,
                'monto' => $multa->valor_plataforma,
                'barrio_id' => null
            ],
        ];

        // Acumula los eventos de auditoría generados durante la iteración
        $auditEventsGenerados = [];

        // Procesa y registra la transferencia simulada para cada tipo de beneficiario
        foreach ($distribuciones as $d) {
            // Persiste el registro de ingreso contable en la base de datos
            $ingreso = IngresoContableMulta::create([
                'multa_id' => $multa->id,
                'beneficiario_tipo' => $d['tipo'],
                'barrio_id' => $d['barrio_id'],
                'porcentaje' => $d['porcentaje'],
                'monto' => $d['monto'],
                'fecha_recepcion' => now(),
                'cuenta_bancaria_destino' => $this->cuentaSimuladaPara($d['tipo'], $d['barrio_id']),
                'banco_destino' => 'Banco Simulado Municipal',
                'referencia_transferencia' => 'TRX-' . Str::upper(Str::random(10)),
                'estado_transferencia' => 'confirmada',
                'comprobante_transferencia' => hash('sha256', $multa->id . $d['tipo'] . now()->timestamp),
                'es_simulado' => true,
            ]);

            // Registra el evento de auditoría para este ingreso específico
            $auditEventsGenerados[] = AuditEvent::logEvent(
                auditable: $ingreso,
                userId: User::getSistemaAdminId(),
                eventType: 'ingreso_contable_generado',
                details: [
                    'multa_id' => $multa->id,
                    'beneficiario_tipo' => $d['tipo'],
                    'monto' => $d['monto'],
                    'cuenta_destino' => $ingreso->cuenta_bancaria_destino,
                    'referencia_transferencia' => $ingreso->referencia_transferencia,
                ],
            );
        }

        // Se asegura de despachar los eventos a la blockchain solo cuando la transacción global en BD confirme con éxito
        DB::afterCommit(function () use ($auditEventsGenerados) {
            foreach ($auditEventsGenerados as $auditEvent) {
                RegistrarEventoBlockchain::dispatch($auditEvent->id);
            }
        });
    }

    /**
     * Determina el número de cuenta bancaria simulada según el tipo de beneficiario e identificador de barrio.
     *
     * @param string $tipo Tipo de beneficiario ('barrio', 'municipio', 'plataforma').
     * @param int|null $barrioId Identificador del barrio (aplica únicamente para beneficiarios de tipo 'barrio').
     * @return string Cadena que representa el número de cuenta bancaria asignado.
     */
    private function cuentaSimuladaPara(string $tipo, ?int $barrioId): string
    {
        return match ($tipo) {
            'barrio' => 'CTA-BARRIO-' . str_pad($barrioId ?? 0, 4, '0', STR_PAD_LEFT),
            'municipio' => 'CTA-MUNICIPIO-001',
            'plataforma' => 'CTA-PLATAFORMA-001',
        };
    }
}
