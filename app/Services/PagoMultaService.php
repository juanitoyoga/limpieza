<?php

namespace App\Services;

use App\Models\Multa;
use Illuminate\Support\Str;

/**
 * Class PagoMultaService
 *
 * Servicio encargado de gestionar y generar la información necesaria
 * para la simulación y registro de pagos de multas.
 *
 * @package App\Services
 */
class PagoMultaService
{
    /**
     * Genera la estructura de datos requerida para registrar el pago de una multa.
     *
     * @param Multa $multa Instancia del modelo Multa sobre la cual se generará el pago.
     * @return array Array asociativo con el método de pago, la referencia única y el comprobante.
     */
    public function generarDatosPago(Multa $multa): array
    {
        return [
            // Define el tipo de método de pago asignado
            'metodo_pago' => 'transferencia_simulada',

            // Genera un código de referencia único estructurado: 'SIM-AAAAMMDD-XXXXXXXX'
            'referencia_pago' => 'SIM-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8)),

            // Genera la cadena de comprobante hash vinculada a la multa
            'comprobante_pago' => $this->generarComprobante($multa),
        ];
    }

    /**
     * Genera un hash único en formato SHA-256 para simular un comprobante bancario.
     *
     * @param Multa $multa Instancia de la multa que contiene la información para el hash.
     * @return string Cadena de 64 caracteres en hexadecimal que representa el comprobante.
     */
    private function generarComprobante(Multa $multa): string
    {
        // Hash SHA-256 basado en el ID de la multa, la referencia original y el timestamp actual
        return hash('sha256', $multa->id . $multa->referencia . now()->timestamp);
    }
}
